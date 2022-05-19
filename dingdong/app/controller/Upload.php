<?php

namespace app\controller;

use app\BaseController;

class Upload extends BaseController
{
    // 上传图片
    public function uploadImg()
    {
        $file = request()->file('file');

        // 检测图片格式
        $ext = $file->getOriginalExtension();
        $extArr = explode('|', 'jpg|png|gif|jpeg');
        if(!in_array($ext, $extArr)){
            return json(['code' => -3, 'data' => '', 'msg' => '只能上传jpg|png|gif|jpeg的文件']);
        }

        // 存到本地
        $saveName = \think\facade\Filesystem::disk('public')->putFile('upload', $file);
        return json(['code' => 200, 'data' => ['src' => config('dingdong.domain') . '/storage/' . $saveName ], 'msg' => '']);
    }
}