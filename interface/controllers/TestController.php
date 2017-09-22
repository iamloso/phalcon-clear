<?php
namespace PFrame\Controllers;

use Phalcon\Mvc\Controller;
use PFrame\Libs\Models\InitializeModel as IntMs;
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
        $result = IntMs::getInstance('Borrow')->getOneData(['conditions' => 'borrow_id=251']);
        var_dump($result);die;
    }

    public function indexAction()
    {
        $result = $this->rpc->request('service_merchant', 'service_name', ["test"=>'aa']);
        var_dump($result);
    }
}
