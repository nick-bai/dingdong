<?php

namespace app\controller;

use app\BaseController;

class Base extends BaseController
{
    protected $userInfo;

    public function initialize()
    {
        crossDomain();

        $this->userInfo = getJWT(getHeaderToken());
        if(empty($this->userInfo)){
            exit(json_encode(['code' => 406, 'data' => [], 'msg' => 'token无效']));
        }
    }
}