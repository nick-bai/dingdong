<?php

namespace app\controller;

use think\facade\Db;

class Customer extends Base
{
    public function index()
    {
        $limit = input('param.limit');
        $customerName = input('param.customer_name');

        $where = [];
        if (empty($name)) {
            $where[] = ['customer_name', 'like', '%' . $customerName . '%'];
        }

        $list = Db::name('customer')->where($where)->order('id desc')->paginate($limit);
        return jsonReturn(0, 'success', $list);
    }

    public function serviceLog()
    {
        $limit = input('param.limit');
        $customerId = input('param.customer_id');

        $where = [];
        if (empty($name)) {
            $where[] = ['customer_id', '=', $customerId];
        }

        $list = Db::name('service_log')->where($where)->order('id desc')->paginate($limit)->each(function ($item) {
            $item['chat_time'] = changeTimeType($item['chat_time']);
            return $item;
        });
        return jsonReturn(0, 'success', $list);
    }

    public function getChatLog()
    {
        $customerId = input('param.customer_id');
        $limit = input('param.limit', 15);
        $log = Db::name('chat_log')->where(function ($query) use ($customerId) {
            $where[] = [
                'from_id', '=', $customerId
            ];

            $whereOr[] = [
                'to_id', '=', $customerId
            ];

            $query->where($where)->whereOr($whereOr);
        })->order('id desc')->paginate($limit)->each(function($item, $key){
            $item['time'] = date('m-d H:i', strtotime($item['create_time']));
            return $item;
        })->toArray();

        sort($log['data']);
        return jsonReturn(0, 'success', $log);
    }
}