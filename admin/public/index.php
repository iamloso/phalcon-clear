<?php

use PFrame\Libs\Common\Common;
use PFrame\Libs\Common\SLog;

define ( 'SITE_NAME', 'product_center' );

define ( 'APP_NAME', 'admin' );

define ( 'PROJECT_PATH', realpath ( '..' ) );

define ( 'ROOT_PATH', realpath ( '../..' ) );

define ( 'PROJECT_NAME',SITE_NAME.'_'.APP_NAME);


/**
 * Read the Configuration
*/
$Config = include ROOT_PATH."/framework/Config.php";

/**
 * Read auto-loader
 */
include ROOT_PATH."/framework/Loader.php";

/**
 * Read services
 */
include ROOT_PATH."/framework/Services.php";

try {
    if ($Config->application->debug) {
        ini_set ( 'display_errors', '1' );
        error_reporting ( E_ALL );
    }else{
        error_reporting ( E_ERROR );
    }

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application ( $di );

    echo $application->handle()->getContent();
} catch ( \Exception $e ) {
    /**
     * Log the exception
     */
    SLog::writeLog( $e, SLog::ERROR, $Config->logFilePath->error );
    /**
     * Show an static error page
    */
    Common::processException($Config->application->debug, $e);
}

