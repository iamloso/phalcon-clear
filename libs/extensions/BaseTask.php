<?php
namespace PFrame\Libs\Extensions;
use PFrame\Libs\Common;

class BaseTask extends \Phalcon\CLI\Task
{
    use Common\ShareTrait;

    public $centerConfig;
    public $dbConfig;
    public $config;
    public $lockFile;

    public function runAction()
    {
        $this->centerConfig = \Phalcon\DI::getDefault()->getShared('centerConfig');
        $this->dbConfig     = \Phalcon\DI::getDefault()->getShared('dbConfig');
        $this->config       = \Phalcon\DI::getDefault()->getShared('config');

        $this->createLockFile();
    }

    /**
     * task类业务日志
     * @param string $log
     * @param string $logType
     * @return null
     */
    public function taskLog($log,$logType='INFO'){
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
        $logPath  = TMP_PATH_LOG.date('Y-m-d').'/task_log/'.$className.'.log';

        $log = $className ." ". $log;
        Common\SLog::writeLog($log,$logType,$logPath);
    }

    /**
     * 创建锁文件
     * @return bool
     */
    public function createLockFile()
    {
        $className = str_replace("\\", "-", get_class($this)); //get_called_class()
        $this->lockFile = TMP_PATH_LOG.date('Y-m-d').'/task_'.$className.'.pid';
        return true;
    }

    /**
     * 跑脚本加锁
     * @param $lockFile
     * @return bool
     */
    public function enterLock($lockFile = ''){
        $lockFile = empty($lockFile) ? $this->lockFile : $this->lockFile.'.'.$lockFile;
        if(empty($lockFile)){
            return false;
        }

        if(file_exists($lockFile)){
            return false;
        }
        $fpLock = fopen( $lockFile, 'w+');
        fclose( $fpLock );

        return true;
    }

    /**
     * 检查跑脚本加锁
     * @param $lockFile
     * @return bool
     */
    public function releaseLock($lockFile = ''){
        $lockFile = empty($lockFile) ? $this->lockFile : $this->lockFile.'.'.$lockFile;
        if (empty($lockFile)){
            return false;
        }
        if(unlink($lockFile)){
            //echo "删除锁文件成功\n";
        } else {
            echo "删除锁文件失败\n";
        }
    }
}
