<?php

namespace app\controller;

use app\validate\KefuValidate;
use app\validate\WordValidate;
use think\exception\ValidateException;
use think\facade\Db;

class Word extends Base
{
    public function index()
    {
        $cateId = input('param.cate_id');
        $limit = input('param.limit');
        $title = input('param.title');

        $where[] = ['cate_id', '=', $cateId];
        if (!empty($title)) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }

        $list = Db::name('word')->where($where)->order('id desc')->paginate($limit);
        return jsonReturn(0, 'success', $list);
    }

    public function add()
    {
        $param = input('post.');

        try {

            validate(WordValidate::class)->check($param);
        } catch (ValidateException $e) {
            return jsonReturn(-1, $e->getError());
        }

        $has = Db::name('word')->field('id')->where([
            'title' => $param['title']
        ])->find();

        if (!empty($has)) {
            return jsonReturn(-2, '该分类内容已经存在');
        }

        $param['create_time'] = date('Y-m-d H:i:s');
        Db::name('word')->insert($param);

        return jsonReturn(0, '添加成功');
    }

    public function edit()
    {
        $param = input('post.');

        try {

            validate(WordValidate::class)->check($param);
        } catch (ValidateException $e) {
            return jsonReturn(-1, $e->getError());
        }

        $where[] = ['title', '=', $param['title']];
        $where[] = ['id', '<>', $param['id']];
        $has = Db::name('word')->field('id')->where($where)->find();

        if (!empty($has)) {
            return jsonReturn(-2, '该分类内容已经存在');
        }

        $param['update_time'] = date('Y-m-d H:i:s');
        Db::name('word')->where('id', $param['id'])->update($param);

        return jsonReturn(0, '编辑成功');
    }

    public function del()
    {
        $id = input('param.id');

        Db::name('word')->where('id', $id)->delete();
        return jsonReturn(0, '删除成功');
    }
}