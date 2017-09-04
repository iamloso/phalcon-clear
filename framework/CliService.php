<?php
use Phalcon\DI\FactoryDefault\CLI as CliDI;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;

use PFrame\Libs\Common\SLog;

// 使用CLI工厂类作为默认的服务容器
$di = new CliDI ();

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set ( 'db', function () use($DbConfig) {
    return new DbAdapter ( array (
        'host' => $DbConfig->database->host,
        'port' => $DbConfig->database->port,
        'username' => $DbConfig->database->username,
        'password' => $DbConfig->database->password,
        'dbname'   => $DbConfig->database->dbname,
            "options" => array (
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'  // 设置编码
                        ) 
    ) );
} );


/**
 * Start the session the first time some component request the session service
 */
$di->set ( 'session', function () {
    $session = new SessionAdapter ();
    $session->start ();
    
    return $session;
} );
/**
 * Register the flash service with custom CSS classes
 */
$di->set ( 'flash', function () {
    return new FlashSession ( array (
            'error' => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice' => 'alert alert-info' 
    ) );
} );

$di->set ( 'config', $Config );
$di->set ( 'dbConfig', $DbConfig );
$di->set ( 'centerConfig', $CenterConfig );
$di->set ( 'slog', function () {
    $SLog = new SLog ();
    return $SLog;
} );

