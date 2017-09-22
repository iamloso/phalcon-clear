<?php
namespace PFrame\Controllers;

use PFrame\Libs\Extensions\RpcController;
use PFrame\Libs\Models\InitializeModel as IntMs;
use PFrame\Libs\Services\InitializeService as IntSvs;

class TestController extends RpcController
{
    public $rules = array(
        'test' => array(
            'borrow_id' => array('filter' => 'PresenceOf', 'message' => 'borrow_id不能为空！'),
            'invest_money' => array('filter' => 'PresenceOf', 'message' => 'money不能为空！'),
        ),
    );

    public function initAction()
    {
        $this->controllerLog(__LINE__ . 'line '.$this->controller.' RPC server init ','INFO');

    }

    public function testAction($data = [])
    {
        $this->controllerLog(__LINE__ . 'line 执行'.$data['controller'].' RPC server '.$data['action'].' method  request params:' .json_encode($data,JSON_UNESCAPED_UNICODE),'INFO');

        $checkMsg = $this->validate($data);
        if ($checkMsg !== true) {
            return $this->setError('100', $checkMsg);
        }

        $result = IntSvs::getInstance('TestService')->test();

        return $this->echoData($result);
    }
}
