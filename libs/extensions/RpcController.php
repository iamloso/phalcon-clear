<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Common\FormValidation;
use PFrame\Libs\Common\Error;

class RpcController extends BaseController
{
    public $controller;
    public $action;
    public $space = 'Controllers';
    public $errNo = '0';
    public $error = '成功';

    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 执行路由前动作
     * @param $dispatcher
     * @return bool
     */
    public function beforeExecuteRoute($dispatcher)
    {
        parent::beforeExecuteRoute($dispatcher);
        $this->controller = $dispatcher->getControllerName();
        $this->action     = $dispatcher->getActionName();
        if ($this->action != 'init') {
            return false;
        }
    }

    /**
     * 执行路由后动作
     * @param $dispatcher
     * @return bool
     */
    public function afterExecuteRoute($dispatcher)
    {
        parent::afterExecuteRoute($dispatcher);
        $namespaces = $this->loader->getNamespaces();
        foreach ($namespaces as $key => $value) {
            if (strpos($key, $this->space) !== false ) {
                $service = $key;
                break;
            }
        }
        if (!empty($this->controller)) {
            $service = $service.'\\'.ucfirst($this->controller).rtrim($this->space, 's');
            $service = new \Yar_Server(new $service());
            $service->handle();
        }
    }

    /**
     * 参数检验
     * @param array $data
     * @return boolean
     */
    public function validate($data)
    {
        $controller = $data['controller'];
        $action     = $data['action'];
        unset($data['controller'], $data['action']);
        if (!empty($this->rules[$action]) && is_array($this->rules[$action])) {
            $rule = $this->rules[$action];
        }
        $validator = new FormValidation($rule);
        $result = $validator->validate($data);
        if (!$result) {
            return $validator->message;
        }
        return $result;
    }

    /**
     * 设置输出数据
     * @param array $data
     */
    protected function echoData($data = [])
    {
        $res['errno'] = intval($this->errNo);
        $res['error'] = $this->error;
        $res["data"]  = $data;
        return $res;
    }

    /**
     * 设置错误信息
     * @param string $errorCode 错误码
     * @param string $errorInfo 错误信息
     * @return bool
     */
    public function setError($errorCode, $errorInfo = '')
    {
        $this->errNo = $errorCode;
        $this->error = $errorInfo;
        return $this->echoData();
    }
}
