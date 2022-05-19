<?php

namespace app\controller;

use think\facade\Db;

class WordCate extends Base
{
    public function index()
    {
        $list = Db::name('word_cate')->select();
        return jsonReturn(0, 'success', $list);
    }

    public function add()
    {
        $param = input('post.');

        if (empty($param['cate_name'])) {
            return jsonReturn(-1, '请输入分类名');
        }

        $has = Db::name('word_cate')->field('id')->where([
            'cate_name' => $param['cate_name']
        ])->find();

        if (!empty($has)) {
            return jsonReturn(-2, '该分类已经存在');
        }

        $param['status'] = 1;
        Db::name('word_cate')->insert($param);

        return jsonReturn(0, '添加成功');
    }

    public function del()
    {
        $id = input('param.id');

        $has = Db::name('word')->field('id')->where([
            'cate_id' => $id
        ])->find();

        if (!empty($has)) {
            return jsonReturn(-1, '该分类下有常用语，不可删除');
        }

        Db::name('word_cate')->where('id', $id)->delete();
        return jsonReturn(0, '删除成功');
    }
}