<?php

namespace app\validate;

use think\Validate;

class KefuValidate extends Validate
{
    protected $rule = [
        'phone|手机号' => 'require|mobile',
        'name|昵称' => 'require',
        'avatar|头像' => 'require',
        'password|密码' => 'require'
    ];

    protected $scene = [
        'edit' => ['phone', 'name', 'avatar']
    ];
}