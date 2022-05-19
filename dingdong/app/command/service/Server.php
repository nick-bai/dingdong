<?php

namespace app\command\service;

use PHPSocketIO\SocketIO;
use Workerman\Timer;
use Workerman\Worker;

class Server
{
    public static function start()
    {
        if (config('dingdong.open_ssl')) {
            $context = config('dingdong.context');
            if (env('SSL.ALLOW_SELF_SIGNED')) {
                $context['ssl']['allow_self_signed'] = true;
            }

            // PHPSocketIO服务
            $io = new SocketIO(config('dingdong.ws_port'), $context);
        } else {
            // PHPSocketIO服务
            $io = new SocketIO(config('dingdong.ws_port'));
        }

        $io->on('workerStart', function() use ($io) {
            $global = new \GlobalData\Client('127.0.0.1:2207');
            $global->onlineKeFu = [];
            $global->onlineCustomer = [];
            $global->serviceMap = [];

            $db = new \Workerman\MySQL\Connection(env('database.hostname'),
                env('database.hostport', 3306), env('database.username'), env('database.password', ''),
                env('database.database', ''), env('database.CHARSET'));

            // 10分钟一次，检测客服是否离线
            Timer::add(10 * 60, function () use ($db, $global) {
                $list = $db->select('id,code')->from(env('database.prefix') . 'kefu')
                    ->where('status = 1')->query();

                $onlineKeFu = $global->onlineKeFu;
                if (!empty($list)) {
                    foreach ($list as $vo) {
                        if (!isset($onlineKeFu[$vo['code']])) {
                            $sql = 'UPDATE `' . env('database.prefix') . 'kefu` SET `status` = 2 WHERE `id` = ' . $vo['id'];
                            $db->query($sql);

                            unset($onlineKeFu[$vo['code']]);
                            $global->onlineKeFu = $onlineKeFu;
                        }
                    }
                }
            });

            // 5分钟一次，检测访客是否离线
            Timer::add(5 * 60, function () use ($db, $global) {
                $list = $db->select('id,customer_id,start_time')->from(env('database.prefix') . 'service_log')
                    ->where('status = 1 and start_time > "' . date('Y-m-d') . '"')->query();

                $onlineCustomer = $global->onlineCustomer;
                $serviceMap = $global->serviceMap;

                if (!empty($list)) {
                    foreach ($list as $vo) {
                        if (!isset($onlineCustomer[$vo['customer_id']])) {
                            $chatTime = time() - strtotime($vo['start_time']);
                            $sql = 'UPDATE `' . env('database.prefix') . 'service_log` SET `status` = 2,`end_time` = "' . date('Y-m-d H:i:s')
                                . '",`chat_time` = ' . $chatTime . ' WHERE `id` = ' . $vo['id'];
                            $db->query($sql);

                            unset($onlineCustomer[$vo['customer_id']]);
                            $global->onlineCustomer = $onlineCustomer;
                            if (isset($serviceMap[$vo['customer_id']])) {
                                unset($serviceMap[$vo['customer_id']]);
                                $global->serviceMap = $serviceMap;
                            }
                        }
                    }
                }
            });

            $io->on('connection', function ($socket) use ($io, $db, $global) {

                // 客服登录
                $socket->on('KEFU_LOGIN', function ($msg) use ($socket, $io, $db, $global) {
                    $data = json_decode($msg, true);

                    $onlineKeFu = $global->onlineKeFu;
                    $onlineKeFu[$data['code']] = $data;
                    $socket->join($data['code']);
                    $socket->uid = $data['code'];
                    $global->onlineKeFu = $onlineKeFu;

                    $db->update(env('database.prefix') . 'kefu')->cols([
                        'status' => 1
                    ])->where('id=' . $data['id'])->query();

                    $io->to($socket->uid)->emit('LOGIN_SUCCESS');
                });

                // 访客登录
                $socket->on('CUSTOMER_LOGIN', function ($msg) use ($socket, $io, $db, $global) {
                    $data = json_decode($msg, true);

                    $onlineCustomer = $global->onlineCustomer;
                    $onlineKeFu = $global->onlineKeFu;
                    $serviceMap = $global->serviceMap;

                    $onlineCustomer[$data['customer_id']] = $data;
                    $global->onlineCustomer = $onlineCustomer;

                    $socket->join($data['customer_id']);
                    $socket->uid = $data['customer_id'];

                    $ip = $socket->conn->remoteAddress;
                    if (!empty($ip)) {
                        $ip = explode(":", $ip)[0];
                    }

                    // 访客登录
                    $kefuInfo = Events::customerLogin($ip, $db, $data, $socket, $onlineKeFu);
                    if (empty($kefuInfo)) {
                        // 客服全部离线
                        $io->to($socket->uid)->emit('KEFU_OFFLINE');
                        return ;
                    } else {
                        $data['online_status'] = 1;
                        $data['unread_msg'] = 0;
                        $data['avatar'] = $data['customer_avatar'];
                        $data['time'] = date('m-d H:i');
                        $data['last_word'] = '';
                        $io->to($kefuInfo['code'])->emit('CUSTOMER_IN', $data);
                    }

                    $serviceMap[$data['customer_id']] = $kefuInfo['code'];
                    $global->serviceMap = $serviceMap;

                    $io->to($socket->uid)->emit('LOGIN_SUCCESS', $kefuInfo);
                });

                // 访客向客服发送消息
                $socket->on('C2S', function ($msg) use ($socket, $io, $db, $global) {

                    $data = json_decode($msg, true);
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $db->insert(env('database.prefix') . 'chat_log')->cols($data)->query();

                    if (isset($global->onlineKeFu[$data['to_id']])) {
                        $data['time'] = date('m-d H:i', strtotime($data['create_time']));
                        $io->to($data['to_id'])->emit('CHAT', $data);
                    }

                    $io->to($socket->uid)->emit('SEND_SUCCESS');
                });

                // 客服向访客发送消息
                $socket->on('S2C', function ($msg) use ($socket, $io, $db, $global) {

                    $data = json_decode($msg, true);
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $db->insert(env('database.prefix') . 'chat_log')->cols($data)->query();

                    if (isset($global->onlineCustomer[$data['to_id']])) {
                        $data['time'] = date('m-d H:i', strtotime($data['create_time']));
                        $io->to($data['to_id'])->emit('CHAT', $data);
                    }

                    $io->to($socket->uid)->emit('SEND_SUCCESS');
                });

                // 断开链接
                $socket->on('disconnect', function () use ($socket, $io, $db, $global) {
                    if (!isset($socket->uid)) {
                        return;
                    }

                    $onlineCustomer = $global->onlineCustomer;
                    $onlineKeFu = $global->onlineKeFu;
                    $serviceMap = $global->serviceMap;

                    if (isset($onlineKeFu[$socket->uid])) {

                        $socket->leave($socket->uid);
                        unset($onlineKeFu[$socket->uid]);
                        $global->onlineKeFu = $onlineKeFu;
                        return ;
                    }

                    $socket->leave($socket->uid);
                    unset($onlineCustomer[$socket->uid]);
                    $global->onlineCustomer = $onlineCustomer;

                    // 通知客服访客离线
                    if (isset($serviceMap[$socket->uid])) {
                        $kefuCode = $serviceMap[$socket->uid];
                        unset($serviceMap[$socket->uid]);
                        $global->serviceMap = $serviceMap;
                        $io->to($kefuCode)->emit('CUSTOMER_OUT', $socket->uid);

                        // 删除当前的服务数据
                        $info = $db->select('id,service_log_id')->from(env('database.prefix') . 'now_service')
                            ->where("customer_id= '" . $socket->uid . "' ")->row();

                        if (!empty($info)) {
                            $db->delete(env('database.prefix') . 'now_service')->where('id=' . $info['id'])->query();

                            // 维护服务日志
                            $logInfo = $db->select('start_time')->from(env('database.prefix') . 'service_log')
                                ->where('`id` = ' . $info['service_log_id'])->row();

                            $db->update(env('database.prefix') . 'service_log')->cols([
                                'end_time' => date('Y-m-d H:i:s'),
                                'chat_time' => time() - strtotime($logInfo['start_time']),
                                'status' => 2
                            ])->where('id=' . $info['service_log_id'])->query();
                        }
                    }

                    // 设置访客离线
                    $db->update(env('database.prefix') . 'customer')->cols([
                        'online_status' => 0
                    ])->where('customer_id="' . $socket->uid . '"')->query();

                });
            });
        });

        Worker::runAll();
    }
}