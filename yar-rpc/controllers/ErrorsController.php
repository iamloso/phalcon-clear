<?php
namespace PFrame\Controllers;

use PFrame\Libs\Extensions\BaseController;

class ErrorsController extends BaseController
{
    public $checkToken = false;

    public function paramsErrorAction()
    {
        $this->setTitle('请求参数非法!');
    }

    public function show404Action()
    {
        $this->setTitle('很抱歉，您要访问的页面不存在！');
    }

    public function show401Action()
    {

    }

    public function show500Action()
    {
        $this->setTitle('服务器异常，请稍后重试');
    }
}

