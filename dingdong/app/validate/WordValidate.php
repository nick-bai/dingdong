<?php

namespace app\validate;

use think\Validate;

class WordValidate extends Validate
{
    protected $rule = [
        'title|标题' => 'require',
        'word|内容' => 'require',
        'cate_id|分类id' => 'require'
    ];
}