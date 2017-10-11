<?php
namespace PFrame\Libs\Common;

use PFrame\Libs\Common\Common;
/**
 * 日志类
 * @author Dqcenter
 *
 */
class SLog {
    /**
     * 日志路径
     * @var unknown
     */
    public static $filePath;
    /**
     * 日志类别：错误
     * @var unknown
     */
    const ERROR = \Phalcon\Logger::ERROR;
    /**
     * 日志类别：信息
     * @var unknown
     */
    const INFO  = \Phalcon\Logger::INFO;
    /**
     *  日志类别：信息
     * @var unknown
     */
    const WARNING = \Phalcon\Logger::WARNING;
    /**
     *  日志类别：调试
     * @var unknown
     */
    const DEBUG = \Phalcon\Logger::DEBUG;
    /**
     *  日志类别：致命的
     * @var unknown
     */
    const CRITICAL = \Phalcon\Logger::CRITICAL;
    /**
     * 日志类别：紧急的
     * @var unknown
     */
    const EMERGENCY = \Phalcon\Logger::EMERGENCY;

    public static $config;

    public static function config()
    {
        if (!isset( self::$config) ) {
            self::$config = include ROOT_PATH . '/framework/Config.php';
        }
    }

    /**
     * 写日志
     * @param string $msg 日志内容
     * @param string $filePath 日志文件路径
     * @param unknown $errorType 错误类别
     * @return bool
     */
    public static function writeLog($msg, $errorType = self::INFO, $filePath=''  ) {
        if (empty($msg)) {
            return false;
        }

        if (empty($filePath) ) {
            self::config();
            if (in_array($errorType,array(self::INFO,self::DEBUG))) {
                $filePath = self::$config->logFilePath->access;
            } else {
                $filePath = self::$config->logFilePath->error;
            }
        }
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            umask(000);
            Common::mkDirs($dir);
        }
        $session = \Phalcon\DI::getDefault()->getShared('session');
        $sessionId = "[".$session-> getId ()."] ";
        try {
            $logger = new \Phalcon\Logger\Adapter\File($filePath);
            $formatter = new \Phalcon\Logger\Formatter\Line();
            $formatter->setDateFormat('Y-m-d H:i:s');
            $logger->setFormatter($formatter);
            if (is_object($msg)) {
                $logger->log($sessionId.$msg->getMessage(),$errorType);
                $logger->log($sessionId.$msg->getTraceAsString(),$errorType);
            } else {
                //$debugInfo = debug_backtrace();
                //$msg = $debugInfo[0]['file'] . ' ('.$debugInfo[0]['line'].')' . ':'.$msg;
                $logger->log($sessionId.$msg,$errorType);
            }
            return true;
        } catch (\Exception $e ) {
            error_log('SLog writeLog faild ---'.json_encode($e->getMessage()));
        }
    }
}
