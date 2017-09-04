<?php
namespace PFrame\Libs\Plugins\Db;

/**
 * 数据库常用方法定义
 */

class Db extends \Phalcon\Mvc\Model{
    public static $Connection;
    /**
     * 数据库连接
     * 
     * @return \Phalcon\Db\Adapter\Pdo\Mysql
     */
    public static function Connection() {
        if( !isset( self::$Connection ) ) {
            self::$Connection = \Phalcon\DI::getDefault()->getShared('db');

        }
        return self::$Connection;
    }

}

