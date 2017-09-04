<?php
namespace PFrame\Libs\Extensions;

use Phalcon\Mvc\Controller;
use PFrame\Libs\Common;

class BaseController extends Controller
{
    use Common\ShareTrait;

    public $encryptData = array();
    public $token;
    public $userId;
    public $aes;
    public $source;

    public function initialize()
    {
        $this->view->setVar('cdnVersion', $this->centerConfig->cdnVersion);
        $this->view->setVar('staticPath', $this->centerConfig->staticPath);
        $this->view->setVar('siteName', trim($this->centerConfig->adminPath, '/'));
        $this->view->setVar('SITE_NAME', SITE_NAME);
    }

    public function setTitle($title)
    {
        $this->tag->setTitle($title);
    }

    /**
     * 执行路由前动作
     * @param $dispatcher
     */
    public function beforeExecuteRoute($dispatcher)
    {

        $params = $this->getAllParams();

        $paramsJson = json_encode ($params, JSON_UNESCAPED_UNICODE);

        $this->controllerLog('request start|params:'.$paramsJson);
        Common\TimerUtil::start('all');
    }

    /**
     * 执行路由后动作
     * @param $dispatcher
     * @return bool
     */
    public function afterExecuteRoute($dispatcher)
    {
        $caString = $dispatcher->getControllerName() . "/" . $dispatcher->getActionName() . " ";

        Common\TimerUtil::stop('all');
        Common\SLog::writeLog($caString . Common\TimerUtil::tree());
        $this->controllerLog('request ended');

        if ($this->config->application->profilerDebug) {
            //获取所有的prifler记录结果，这是一个数组，每条记录对应一个sql语句
            $profiles = $this->di->get('profiler')->getProfiles();
            if ($profiles) {
                $profileMsg = "\n\n******************" . $caString . " profiler begin *******************\n";
                //遍历输出
                foreach ($profiles as $profile) {
                    $profileMsg .= "\nSQL语句: " . $profile->getSQLStatement() . "\n";
                    $profileMsg .= "开始时间: " . $profile->getInitialTime() . "\n";
                    $profileMsg .= "结束时间: " . $profile->getFinalTime() . "\n";
                    $profileMsg .= "消耗时间: " . $profile->getTotalElapsedSeconds() . "\n";
                }
                $profileMsg .= "\n******************" . $caString . " profiler ended *********************\n";
                $profileLogFile = $this->config->logFilePath->profile;
                Common\SLog::writeLog($profileMsg, Common\SLog::DEBUG, $profileLogFile);
            }
        }
        return true;
    }

    /**
     * controller层日志
     * @param string $log
     * @param string $logType
     * @return null
     */
    public function controllerLog($log,$logType='INFO'){
        if( $logType == 'ERROR' ) {
            $logType = Common\SLog::ERROR;
        } elseif ( $logType == 'WARNING' ) {
            $logType = Common\SLog::WARNING;
        } elseif ( $logType == 'DEBUG' ) {
            $logType = Common\SLog::DEBUG;
        } elseif ( $logType == 'CRITICAL' ) {
            $logType = Common\SLog::CRITICAL;
        } else {
            $logType = Common\SLog::INFO;
        }
        $className = str_replace("\\", "-", get_class($this)); //get_called_class()
        $logPath   = TMP_PATH_LOG.date('Y-m-d').'/controller_log/'.$className.'.log';
        $function  = $this->dispatcher->getActionName();
        $log = $className.'::'.$function.'Action'.' '.$log;
        Common\SLog::writeLog($log,$logType,$logPath);
    }

    public function getAllParams(){
        return $this->dispatcher->getParams()+$this->request->get()+$this->encryptData;
    }

    /**
     * 解析 data tm 数据
     */
    public function parseEncryptParams()
    {
        $paramData = $this->getAllParams();
        if (!$this->config->application->debug) {
            if (empty($paramData['data']) || empty($paramData['tm'])) {
                return false;
            }
        }
        if (!empty($paramData['data']) && !empty($paramData['tm'])) {
            $data = $paramData['data'];
            $tm   = $paramData['tm'];

            $tm   = str_replace(" ","+",$tm);
            $data = str_replace(" ","+",$data);
            $source = empty($paramData['source']) ? '' : $paramData['source'];

            $this->source = $source;// 来源
            $rsa  = new Common\Rsa($source);

            $aesKey = $rsa->decrypt($tm);

            $this->aes = new Common\CryptAES($aesKey);
            if($this->source == 'WEB') {
                $data = str_replace('\n', '', $data);
                $data = $this->aes->decryptCBC($data);
            }
            else{
                $data = $this->aes->decrypt($data);
            }
            parse_str($data,$result);
            if ($this->config->application->debug) {
                $this->controllerLog('Request Params tm:'.$tm.' data:'.$data, 'DEBUG');
                $this->controllerLog('Request Params key'.$aesKey, 'DEBUG');
                $this->controllerLog('Decrypt Params :'.json_encode($result), 'DEBUG');
            }
            $this->encryptData = $this->dispatcher->getParams()+$result;
            if (!empty($this->encryptData['borrow_id'])) {
                $mcrypt = new Common\Mcrypt();
                $borrowId = $mcrypt->decryptBase64($this->encryptData['borrow_id']);
                preg_match('/\d+/',$borrowId,$info);
                $this->encryptData['borrow_id'] = $info[0];
            }
            if (!empty($this->encryptData['token'])) {
                $this->token = $this->encryptData['token'];
                return $this->getTokenInfo();
            }
        }
        return true;
    }

    /**
     * 获取token中信息
     * @return bool
     */
    public function getTokenInfo()
    {
        /**
         * 解密
         */
        $crypt = new Common\Mcrypt();
        $decryptToken = $crypt->decryptBase64($this->token);
        if ($decryptToken == false) {
            $this->controllerLog('getTokenInfo failed!====='.json_encode(array('token'=>$this->token,'msg'=>'token is invalid!')));
            return false;
        }
        @list($this->userId, $expiredAt, $cryptParams) = explode('_', $decryptToken);
        /**
         * 验证是否过期
         */
        if (time() > $expiredAt) {
            $this->controllerLog('getTokenInfo failed!====='.json_encode(array('token'=> $this->token,'expired_at'=>date('Y-m-d H:i:s'), 'msg'=>'User token had expired!')));
            return false;
        }

        if (empty($this->userId)) {
            $this->controllerLog('getTokenInfo failed!====='.json_encode(array('token'=> $this->token,'expired_at'=>date('Y-m-d H:i:s'), 'msg'=>'User Id is empty!')));
            return false;
        }
        return true;
    }

}