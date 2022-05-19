<?php

namespace app\controller;

use app\validate\KefuValidate;
use think\exception\ValidateException;
use think\facade\Db;

class Kefu extends Base
{
    public function index()
    {
        $limit = input('param.limit');
        $name = input('param.name');

        $where[] = ['is_del', '=', 1];
        if (empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $list = Db::name('kefu')->where($where)->order('id desc')->paginate($limit);
        return jsonReturn(0, 'success', $list);
    }

    public function add()
    {
        $param = input('post.');

        try {

            validate(KefuValidate::class)->check($param);
        } catch (ValidateException $e) {
            return jsonReturn(-1, $e->getError());
        }

        $has = Db::name('kefu')->field('id')->where([
            'phone' => $param['phone']
        ])->find();

        if (!empty($has)) {
            return jsonReturn(-2, '该手机号已经存在');
        }

        $param['status'] = 2;
        $param['code'] = uniqid();
        $param['password'] = md5($param['password'] . config('dingdong.salt'));
        $param['create_time'] = date('Y-m-d H:i:s');

        Db::name('kefu')->insert($param);

        return jsonReturn(0, '添加成功');
    }

    public function edit()
    {
        $param = input('post.');

        try {

            validate(KefuValidate::class)->scene('edit')->check($param);
        } catch (ValidateException $e) {
            return jsonReturn(-1, $e->getError());
        }

        $where[] = ['phone', '=', $param['phone']];
        $where[] = ['id', '<>', $param['id']];
        $has = Db::name('kefu')->field('id')->where($where)->find();

        if (!empty($has)) {
            return jsonReturn(-2, '该手机号已经存在');
        }

        if (isset($param['password'])) {
            $param['password'] = md5($param['password'] . config('dingdong.salt'));
        } else {
            unset($param['password']);
        }

        $param['update_time'] = date('Y-m-d H:i:s');

        Db::name('kefu')->where('id', $param['id'])->update($param);

        return jsonReturn(0, '编辑成功');
    }

    public function del()
    {
        $id = input('param.id');
        Db::name('kefu')->where('id', $id)->update([
            'is_del' => 2,
            'update_time' => date('Y-m-d H:i:s')
        ]);

        return jsonReturn(0, '删除成功');
    }

    public function getMenuList()
    {
        $info = Db::name('kefu')->where('id', $this->userInfo['id'])->find();

        $data = [
            'id' => $this->userInfo['id'],
            'name' => $this->userInfo['name'],
            'avatar' => $info['avatar'],
            'roleId' => 1,
            'menu' => []
        ];

        return jsonReturn(0, '获取成功', $data);
    }

    public function getUserInfo()
    {
        return jsonReturn(0, 'success', Db::name('kefu')
            ->field('id,code,name,avatar')->where('id', $this->userInfo['id'])->find());
    }
}