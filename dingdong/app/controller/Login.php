<?php

namespace app\controller;

use think\facade\Db;

class Login extends Base
{
    public function initialize()
    {
        crossDomain();
    }

    public function index()
    {
        $param = input('post.');

        $info = Db::name('kefu')->where('phone', $param['phone'])->where('is_del', 1)->find();
        if (empty($info)) {
            return jsonReturn(-1, '用户名密码错误');
        }

        if (md5($param['password'] . config('dingdong.salt')) != $info['password']) {
            return jsonReturn(-2, '用户名密码错误');
        }

        $token = setJWT([
            'id' => $info['id'],
            'code' => $info['code'],
            'name' => $info['name']
        ]);

        Db::name('kefu')->where('id', $info['id'])->update([
            'last_login_time' => date('Y-m-d H:i:s'),
            'last_ip' => request()->ip()
        ]);

        return jsonReturn(0, '登录成功', [
            'token' => (string)$token,
            'userInfo' => [
                'id' => $info['id'],
                'role_id' => 1,
                'code' => $info['code'],
                'name' => $info['name'],
                'avatar' => $info['avatar'],
            ]
        ]);
    }

    public function getSocket()
    {
        $data = [
            'socket' => config('dingdong.domain') . ':' . config('dingdong.ws_port')
        ];

        return jsonReturn(0, 'success', $data);
    }

    public function getChatLog()
    {
        $customerId = input('param.customer_id');
        $kefuCode = input('param.code');
        $limit = input('param.limit', 15);
        $log = Db::name('chat_log')->where(function ($query) use ($customerId, $kefuCode) {
            $where[] = [
                'from_id', '=', $customerId
            ];
            $where[] = [
                'to_id', '=', $kefuCode
            ];

            $query->where($where);
        })->whereOr(function ($query) use ($customerId, $kefuCode) {
            $where[] = [
                'from_id', '=', $kefuCode
            ];
            $where[] = [
                'to_id', '=', $customerId
            ];
            $query->where($where);
        })->order('id desc')->paginate($limit)->each(function($item, $key){
            $item['time'] = date('m-d H:i', strtotime($item['create_time']));
            return $item;
        })->toArray();

        sort($log['data']);
        return jsonReturn(0, 'success', $log);
    }
}