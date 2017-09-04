<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Common\FormValidation;
use PFrame\Libs\Common\Error;

class InterfaceController extends BaseController
{
    /**
     * 错误码
     * @var int
     */
    public $errNo = 0;
    /**
     * 错误信息
     * @var string
     */
    public $error = '';
    /**
     * 输出json数据
     * @var array
     */
    public $jsonData = array();
    /**
     * 平台来源
     * @var string
     */
    public $platform = '';

    public $rules = array();

    public $_platform = array(
        'android' => 'PLATFORM_ANDROID',
        'ios' => 'PLATFORM_IOS',
        'pc' => 'PLATFORM_PC_WEB',
        'h5' => 'PLATFORM_H5_WEB',
        'admin' => 'PLATFORM_ADMIN',
    );

    public $action;

    public $controller;

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
        if ($this->parseEncryptParams() == false) {
            $this->setError('ERR_PARAMS_ILLEGAL', 'token解析失败');
            $this->returnJson();
            return false;
        }
        $this->controller = $dispatcher->getControllerName();
        $this->action = $dispatcher->getActionName();
        if (!empty($this->baseRules)) {
            if (!$this->validate($this->baseRules)) {
                $this->returnJson();
                return false;
            }
        }
        if (!empty($this->rules[$this->action]) && is_array($this->rules[$this->action])) {
            $rule = $this->rules[$this->action];
            if (!$this->validate($rule)) {
                $rule['t_platform'] = array('filter' => 'PresenceOf', 'message' => 't_platform不能为空！');
                $this->returnJson();
                return false;
            }
        }
        $this->platform = strtolower($this->getParam('t_platform'));
        if (!isset($this->_platform[$this->platform])) {
            $this->setError('ERR_PARAMS_ILLEGAL', '平台来源未知');
            $this->returnJson();
            return false;
        } else {
            $this->platform = $this->_platform[$this->platform];
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

        if (is_array($this->jsonData) && empty($this->jsonData)) {
            $this->jsonData = (object)$this->jsonData;
        }
        $this->returnJson();
    }


    /**
     * 输出json数据
     * @param bool $plainFlag
     */
    protected function returnJson($plainFlag = false)
    {
        $this->view->setRenderLevel(\Phalcon\Mvc\View::LEVEL_NO_RENDER);
        $res['errno'] = intval($this->errNo);
        $res['error'] = $this->error;
        $res["data"] = $this->jsonData;

        if ($plainFlag) {
            if (strpos(isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '', 'application/json') !== false) {
                header('Content-type:text/plain; charset=utf-8');
            }
        } else {
            header("Content-type:application/json; charset=utf-8");
        }
        $jsonData = json_encode($res, JSON_UNESCAPED_UNICODE);
        if ($this->config->application->debug) {
            $this->controllerLog('return json :' . $jsonData, 'DEBUG');
        }

        if ($this->source != 'WEB') {
            if (!empty($this->aes) && !in_array($this->controller . '/' . $this->action, array('borrow/detail', 'borrow/borrowInvest'))) {
                $jsonData = $this->aes->encrypt($jsonData);
            }
        } else {
            $jsonData = $this->aes->encryptCBC($jsonData);
        }
        $dataType = $this->getParam('data_type');
        if ($dataType == 'jsonp') {
            $func = "jsoncallback";
            $callBack = $this->getParam('jsoncallback');
            if (!empty($callBack)) {
                $func = $callBack;
            }
            if ($this->source != 'WEB') {
                echo $func . '(' . $jsonData . ')';
            } else {
                echo $func . "('" . ($jsonData) . "')";
            }
        } else if ($dataType == 'html') {
            return;
        } else {
            echo $jsonData;
        }
    }

    /**
     * 参数检验
     * @param array $rules
     * @return boolean
     */
    public function validate($rules)
    {
        $validator = new FormValidation($rules);
        $result = $validator->validate($this->getAllParams());
        if (!$result) {
            $this->setError('ERR_PARAMS_ILLEGAL', $validator->message);
        }
        return $result;
    }

    /**
     * 设置错误信息
     * @param string $errorCode 错误码
     * @param string $errorInfo 错误信息
     * @return bool
     */
    public function setError($errorCode, $errorInfo = '')
    {
        $arr = Error::get($errorCode);
        $this->errNo = $arr['errNo'];
        $this->error = empty($errorInfo) ? $arr['error'] : $arr['error'] . '|' . $errorInfo;
        if ($errorCode == 'ERR_USER_DEFINED' && !empty($errorInfo)) {
            $this->error = $errorInfo;
        }
        $this->controllerLog($this->errNo . ':' . $this->error);
        return false;
    }


    /**
     * 获取参数
     * 加密接口只支持获取data里的数据
     * @param flag false 按不加密字段
     * @param key
     * @return null
     */
    public function getParam($key)
    {
        if (!empty($this->encryptData) && !empty($this->encryptData[$key])) {
            $value = $this->encryptData[$key];
        } elseif ($this->request->getPost($key) !== null) {
            $value = $this->request->getPost($key);
        } elseif ($this->request->getQuery($key) !== null) {
            $value = $this->request->getQuery($key);
        } elseif ($this->dispatcher->getParam($key) !== null) {
            $value = $this->dispatcher->getParam($key);
        } else {
            return null;
        }
        $pattern = "/<[^>]+>(.*)<\/[^>]+>/";
        $pregRes = preg_match($pattern, $value, $matches);
        if ($pregRes) {
            $this->redirect('/errors/paramsError');
        }
        return $value;
    }

    /**
     * header跳转/提示消息js跳转
     * @param string $controllerAction
     * @param string $info
     */
    public function redirect($controllerAction = '/index/index', $info = '')
    {
        header("Content-type: text/html; charset=utf-8");
        if ($info) {
            echo "<script>alert('" . str_replace('\'', '`', $info) . "');window" . ".location.href='" . $controllerAction . "';</script>";
            exit;
        }
        header("location:$controllerAction");
        exit;
    }

    /**
     * 控制器内跳转、页面不刷新
     * @param string $controllerAction
     * @param $params array
     * @return bool
     */
    public function forward($controllerAction = 'index/index', $params = array())
    {
        $route = explode('/', $controllerAction);
        $this->dispatcher->forward(array(
            'controller' => $route['0'],
            'action' => $route['1'],
            'params' => $params,
        ));
        return false;
    }
}