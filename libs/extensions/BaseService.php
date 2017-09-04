<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Common;
use PFrame\Libs\Plugins\Cache;

class BaseService {
    use Common\ShareTrait;

    private static $arrInstance;
    public $redisObj;
    public $centerConfig;
    public $dbConfig;
    public $config;

    private function __clone(){}
    
    public function __construct() {
        $this->centerConfig = \Phalcon\DI::getDefault()->getShared('centerConfig');
        $this->dbConfig     = \Phalcon\DI::getDefault()->getShared('dbConfig');
        $this->config       = \Phalcon\DI::getDefault()->getShared('config');
        $this->redisObj     = new Cache\RedisCache($this->dbConfig);
    }
    
    /**
     * 支持多个对象的单例 
     */
    static public function getInstance(){ 
        $className = get_called_class();
        if(!isset(self::$arrInstance[$className])){
            self::$arrInstance[$className] = new $className();
        }
        return self::$arrInstance[$className];
    }
    
    /**
     * Service类中写参数日志
     * @param unknown $class
     * @param unknown $func
     * @param unknown $line
     * @param unknown $args
     * @param bool    $isValidArgs
     * 是否有效参数
     * @return bool
     */
    public function serviceTraceLog($class, $func, $line, $args, $isValidArgs = false ){
        $ReflectionClass  = new \ReflectionMethod ($class,$func);
        $log = $func." ($line) ";
        $params = $ReflectionClass->getParameters();
        $validLog = $class.'::'.$func.'('.$line.'):' ;
        $validLogMark = false;
        foreach ($params as $key=>$value) {
            $log .=  $value->name .":" .(isset($args[$key])? print_r($args[$key],true): ' '). "--";
            if ($isValidArgs) {
                if (empty($args[$key])) {
                    $validLogMark = true;
                    $validLog .= $key.' key is empty!, ';
                }
            }
        }
        $this->serviceLog($log);
        if($isValidArgs){
            if ($validLogMark) {
                $this->serviceLog($validLog, 'ERROR');
                return false;
            }
        }
        return true;
    }
    
    /**
     * Service类业务日志
     * @param string $log
     * @param string $logType
     * @return null
     */
    public function serviceLog($log,$logType='INFO'){
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
        $logPath  = TMP_PATH_LOG.date('Y-m-d').'/services_log/'.$className.'.log';

        $log = $className ." ". $log;  
        Common\SLog::writeLog($log,$logType,$logPath);
    }
}