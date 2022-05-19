<?php

namespace app\command\service;

class Events
{
    public static function customerLogin($ip, $db, $data, $socket, $onlineKeFu)
    {
        $location = getLocationByIp($ip, 2);

        $has = $db->select('id')->from(env('database.prefix') . 'customer')
            ->where('customer_id="' . $data['customer_id'] . '"')->row();
        if (!empty($has)) {
            $db->update(env('database.prefix') . 'customer')->cols([
                'customer_id' => $data['customer_id'],
                'client_id' => $socket->id,
                'customer_name' => $data['customer_name'],
                'customer_avatar' => $data['customer_avatar'],
                'customer_ip' => $ip,
                'online_status' => 1,
                'province' => $location['province'],
                'city' => $location['city']
            ])->where('id=' . $has['id'])->query();
        } else {
            $db->insert(env('database.prefix') . 'customer')->cols([
                'customer_id' => $data['customer_id'],
                'client_id' => $socket->id,
                'customer_name' => $data['customer_name'],
                'customer_avatar' => $data['customer_avatar'],
                'customer_ip' => $ip,
                'create_time' => date('Y-m-d H:i:s'),
                'online_status' => 1,
                'province' => $location['province'],
                'city' => $location['city']
            ])->query();
        }

        // 分配客服
        if (empty($onlineKeFu)) {
            return [];
        }

        $kefuInfo = self::distribution($db, $data, $onlineKeFu);
        if (empty($kefuInfo)) {
            return [];
        }

        // 记录服务日志
        $logId = $db->insert(env('database.prefix') . 'service_log')->cols([
            'customer_id' => $data['customer_id'],
            'client_id' => $socket->uid,
            'customer_name' => $data['customer_name'],
            'customer_avatar' => $data['customer_avatar'],
            'customer_ip' => $ip,
            'kefu_code' => $kefuInfo['code'],
            'kefu_name' => $kefuInfo['name'],
            'start_time' => date('Y-m-d H:i:s')
        ])->query();

        // 记录服务的客服
        $db->update(env('database.prefix') . 'customer')
            ->cols([
                'pre_kefu_id' => $kefuInfo['id']
            ])->where('customer_id=' . $data['customer_id'])->query();

        // 检测服务数据是否存在
        $has = $db->select('id')->from(env('database.prefix') . 'now_service')
            ->where('kefu_id="' . $kefuInfo['id'] . '" AND customer_id="' . $data['customer_id'] . '"')
            ->row();
        if(!empty($has)) {

            $db->update(env('database.prefix') . 'now_service')
                ->cols([
                    'kefu_id' => $kefuInfo['id'],
                    'client_id' => $socket->id,
                    'service_log_id' => $logId,
                    'create_time' => time()
                ])->where('id=' . $has['id'])->query();
        } else {

            $db->insert(env('database.prefix') . 'now_service')->cols([
                'kefu_id' => $kefuInfo['id'],
                'customer_id' => $data['customer_id'],
                'client_id' => $socket->id,
                'service_log_id' => $logId,
                'create_time' => time()
            ])->query();
        }

        return $kefuInfo;
    }

    protected static function distribution($db, $data, $onlineKeFu)
    {
        // 上次服务的客服
        $customerInfo = $db->select('kefu_code')->from(env('database.prefix') . 'service_log')
            ->where("customer_id= '" . $data['customer_id'] . "' ")->row();
        if (!empty($customerInfo)) {

            // 上次的客服不在线,重新分配
            if (empty($customerInfo['kefu_code'])) {
                return self::circleDistribution($db, $onlineKeFu);
            } else {

                if (isset($onlineKeFu[$customerInfo['kefu_code']])) {
                    return $onlineKeFu[$customerInfo['kefu_code']];
                }
            }
        } else {

            // 第一次访问,重新分配
            return self::circleDistribution($db, $onlineKeFu);
        }
    }

    protected static function circleDistribution($db, $onlineKeFu)
    {
        $db->beginTrans();

        try {

            // TODO 此处我的数组分配的情况放在mysql的，可以改成 redis 等更高效率的db中
            $sql = 'SELECT `kefu_map` FROM `' . env('database.prefix') . 'kefu_distribution` FOR UPDATE';
            $keFuInfo = $db->query($sql);

            $diffMap = [];
            foreach ($onlineKeFu as $vo) {
                $diffMap[$vo['id']] = $vo;
            }

            if (empty($keFuInfo['0']['kefu_map'])) {
                $kefuMap = [];
            } else {
                $kefuMap = json_decode($keFuInfo['0']['kefu_map'], true);
            }

            if (empty($kefuMap)) {
                $oldMap = [];
                // 刷新在线的客服列表
                foreach ($kefuMap as $key => $vo) {
                    if (!isset($diffMap[$vo['id']])) {
                        unset($kefuMap[$key]);
                    } else {
                        $oldMap[$vo['id']] = 1;
                        $kefuMap[$key] = $diffMap[$vo['id']];
                        unset($diffMap[$vo['id']]);
                    }
                }

                // 将新加入的客服，补充到待接待的队列前端
                foreach($diffMap as $key => $vo) {
                    if (!isset($oldMap[$key])) {
                        array_unshift($kefuMap, $vo);
                    }
                }

                unset($diffMap, $oldMap);
            } else {
                $kefuMap = $onlineKeFu;
            }

            $returnKeFu = array_shift($kefuMap);
            array_push($kefuMap, $returnKeFu);

            $sql = "UPDATE `" . env('database.prefix') . "kefu_distribution` SET `kefu_map` = '"
                . json_encode($kefuMap, JSON_UNESCAPED_UNICODE) . "'";
            $db->query($sql);
            $db->commitTrans();

            if (empty($returnKeFu)) {
                return [];
            }

            return $returnKeFu;
        } catch (\Exception $e) {

            $db->rollBackTrans();

            return [];
        }
    }
}