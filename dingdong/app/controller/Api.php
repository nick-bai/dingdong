<?php

namespace app\controller;

use think\facade\Db;

class Api extends Base
{
    public function getNowService()
    {
        $list = Db::name('now_service')->where('kefu_id', $this->userInfo['id'])->select()->toArray();
        $customerIds = [];
        foreach ($list as $key => $vo) {
            $list[$key]['time'] = date('m-d H:i', $vo['create_time']);
            $list[$key]['last_word'] = '';
            $list[$key]['unread_msg'] = 0;
            $list[$key]['online_status'] = 1;
            $customerIds[] = $vo['customer_id'];
        }

        $customerList = Db::name('customer')->field('customer_id,customer_name,customer_avatar,online_status')
            ->whereIn('customer_id', $customerIds)->select();

        $customerMap = [];
        foreach ($customerList as $vo) {
            $customerMap[$vo['customer_id']] = $vo;
        }

        foreach ($list as $key => $vo) {
            if (isset($customerMap[$vo['customer_id']])) {
                $list[$key]['customer_name'] = $customerMap[$vo['customer_id']]['customer_name'];
                $list[$key]['avatar'] = $customerMap[$vo['customer_id']]['customer_avatar'];
            }
        }

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
            $where[] = [
                'to_id', '=', $this->userInfo['code']
            ];

            $query->where($where);
        })->whereOr(function ($query) use ($customerId) {

            $where[] = [
                'from_id', '=', $this->userInfo['code']
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

    public function getCustomerInfo()
    {
        $customerId = input('param.customer_id');
        $info = Db::name('customer')->where('customer_id', $customerId)->find();

        return jsonReturn(0, 'success', $info);
    }
}