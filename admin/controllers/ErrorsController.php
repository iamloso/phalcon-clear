<?php
namespace PFrame\Controllers;

use PFrame\Libs\Extensions\BaseController;

/**
 * 错误处理类
 * Class ErrorsController
 * @package PFrame\Controllers
 */
class ErrorsController extends BaseController
{
    public $accessCheck = true;

    public function indexAction()
    {
    }
    
    public function show404Action()
    {
        die('aa');
    }

    //角色没有权限
    public function show401Action($msg="")
    {
        $this->view->setVar("msg",  $msg );
    }
    
    public function show500Action()
    {
    }

    public function paramsErrorAction()
    {
        $this->setTitle('请求参数非法!');
    }
}

