<?php
namespace PFrame\Libs\Plugins\Db;

use PFrame\Libs\Common\Common;
use Phalcon\Mvc\User\Plugin;
use PFrame\Libs\Common\SLog;

class DbListener extends Plugin {
    protected $_logfile ;
    protected $_profiler;
    
    /**
     * 构造函数
     * @param bool $profiler
     */
    public function __construct($profiler = false) {
        if ($profiler) {
            $this->_profiler = $profiler;
        } else {
            $this->_profiler = new \Phalcon\Db\Profiler ();
        }
        $this->_logfile = $this->config->logFilePath->db;
    }
    
    /**
     * sql执行前事件函数
     * @param unknown $event
     * @param unknown $connection
     * @return boolean
     */
    public function beforeQuery($event, $connection) {
        $this->_profiler->startProfile ( $connection->getSQLStatement () );
        $sql = $connection->getSQLStatement ();
        $vars = $connection->getSQLVariables ();
        $real_sql = $this->instanceSQLStatement($sql, $vars);
        SLog::writeLog ($real_sql, SLog::DEBUG,  $this->_logfile );
        return true;
    }

    /**
     * sql执行后事件函数
     * @return boolean
     */
    public function afterQuery() {
        $this->_profiler->stopProfile ();
        return true;
    }
    
    /**
     * 替换sql中未绑定的变量
     * @param unknown $sql 未绑定变量的sql
     * @param unknown $vars 需要绑定的参数
     * @return string
     */
    public function instanceSQLStatement($sql, $vars) {
        
        if ($vars) {
            $keys = array();
            $values = array();
            foreach ($vars as $placeHolder=>$var) {
                // fill array of placeholders
                if (is_string($placeHolder)) {
                    $keys[] = '/:'.ltrim($placeHolder, ':').'/';
                } else {
                    $keys[] = '/[?]/';
                }
                /* fill array of values
                It makes sense to use RawValue only in INSERT and UPDATE queries and only as values
                in all other cases it will be inserted as a quoted string*/
                if ((strpos($sql, 'INSERT') === 0 || strpos($sql, 'UPDATE') === 0) && $var instanceof \Phalcon\Db\RawValue) {
                    $var = $var->getValue();
                } elseif (is_null($var)) {
                    $var = 'NULL';
                } elseif (is_numeric($var)) {
                    $var = $var;
                } else {
                    $var = '"'.$var.'"';
                }
                $values[] = $var;
            }
            $sql = preg_replace($keys, $values, $sql, 1);
        }
        
        return $sql;
    }
}