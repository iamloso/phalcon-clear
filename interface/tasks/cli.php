<?php
use Phalcon\CLI\Console as ConsoleApp;
use PFrame\Libs\Common\SLog;

error_reporting(E_ERROR);
// 定义应用目录路径
defined('APP_CLI_PATH') || define('APP_CLI_PATH', realpath(dirname(__FILE__)));

define ( 'SITE_NAME', 'product_center' );

define ( 'APP_NAME', 'interface_task' );

define ( 'PROJECT_NAME', SITE_NAME.'_'.APP_NAME);

define ( 'PROJECT_PATH', realpath ( '..' ) );

define ( 'ROOT_PATH', APP_CLI_PATH.'/../..');
$Config = include ROOT_PATH . "/framework/Config.php";

$DbConfig = include PROJECT_PATH."/config/DbConfig.php";
$CenterConfig = include PROJECT_PATH."/config/CenterConfig.php";

/**
 * Read auto-loader
 */
include ROOT_PATH . "/framework/CliLoader.php";

/**
 * Read services
 */
include ROOT_PATH . "/framework/CliService.php";

/**
 * 处理console应用参数
 */
$arguments = array();

foreach($argv as $k => $arg) {
    if($k == 1) {
        $arguments['task'] = $arg;
    } elseif($k == 2) {
        $arguments['action'] = $arg;
    } elseif($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// 定义全局的参数， 设定当前任务及动作
define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

SLog::writeLog("Task Run : time_start:".date("Y-m-d H:i:s")." task:". CURRENT_TASK . " action:".CURRENT_ACTION. " params:". json_encode( $arguments['params']),SLog::INFO );
try {
    $console = new ConsoleApp();
    $console->setDI($di);
    // 处理参数
    $console->handle($arguments);
} catch (\Phalcon\Exception $e){
    SLog::writeLog ( $e, $Config->logFilePath->error );
    echo $e->getMessage();
    exit(255);
}
SLog::writeLog ("Task Run : time_end:".date("Y-m-d H:i:s")." task:". CURRENT_TASK . " action:".CURRENT_ACTION. " params:". json_encode( $arguments['params']) ,SLog::INFO );

?>