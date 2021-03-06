<?php

/**
 * 数据库配置
 * Class CenterConfig
 */
class DbConfig extends \Phalcon\Config
{
    public function __construct(array $arrayConfig)
    {
        parent::__construct($arrayConfig);
    }
}

return new DbConfig(array(
    'database' => array(
        'adapter'      => 'Mysql',
        'host'         => '10.20.70.215',
        'username'     => 'root',
        'password'     => '',
        'dbname'       => 'dqcenter',
        'port'         => '3306',
    ),

    //商户配置表
    'dbIcloud' => array(
        'adapter'     => 'Mysql',
        'host'        => '10.20.70.215',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'icloud',
    ),

    'redis' => array(
        'host'      => 'redis.p2pyun.cn',
        'port'      => 6379,
        'lifetime'  => 3600,
        'selectdb'  => 10,
        'prefix'    => 'DQCENTER_',
    ),
));
