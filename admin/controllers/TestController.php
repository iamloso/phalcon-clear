<?php
namespace PFrame\Controllers;

use Phalcon\Mvc\Controller;
use PFrame\Libs\InitializeModel as IntMs;
use PFrame\Libs\Services\InitializeService as IntSvs;

/**
 * 测试路由
 * Class TestController
 * @package PFrame\Controllers
 */
class TestController extends controller
{
    public function testAction()
    {
        die('welcome to admin Hello world!');
    }
}
