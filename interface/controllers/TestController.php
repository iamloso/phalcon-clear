<?php
namespace PFrame\Controllers;

use Phalcon\Mvc\Controller;
use PFrame\Libs\InitializeModel as IntMs;
use PFrame\Libs\Services\InitializeService as IntSvs;

class TestController extends Controller
{
    public $flag = true;

    public function initialize()
    {
        if ($this->flag == false) {
            exit('拒绝请求!');
        }
    }

    public function testAction()
    {
        die('Hello world!');
    }
}
