<?php
//设置默认时区
date_default_timezone_set("PRC");

if(!defined('TMP_PATH')) { define ( 'TMP_PATH','/tmp/');}

if(!defined('PROJECT_NAME')) { define ( 'PROJECT_NAME',"project_center");}

if(!defined('TMP_PATH_LOG')) { define ( 'TMP_PATH_LOG',TMP_PATH.'logs/'.PROJECT_NAME.'/');}

/**
 * phalcon框架配置
 */
return new \Phalcon\Config(array(
    'application' => array(
        'controllersDir' =>  PROJECT_PATH.'/controllers/',
        'viewsDir'       =>  PROJECT_PATH.'/views/',
        'modelsDir'      =>  ROOT_PATH.'/libs/models/',
        'serviceDir'     =>  ROOT_PATH.'/libs/services/',
        'pluginsDir'     =>  ROOT_PATH.'/libs/plugins/',
        'commonDir'      =>  ROOT_PATH.'/libs/common/',
        'extDir'         =>  ROOT_PATH.'/libs/extensions/',
        'redisCahceDir'  =>  ROOT_PATH.'/libs/plugins/cache/',
        'db'             =>  ROOT_PATH.'/libs/plugins/db/',
        'admin'          =>  ROOT_PATH.'/libs/plugins/admin/',
        'tasksDir'       =>  PROJECT_PATH.'/tasks/',
        'cacheDir'       =>  TMP_PATH . PROJECT_NAME.'/',
        'baseUri'        => '/',
        'staticBaseUri'  => '/assets/',
        'debug'          => true, //调试开关
        'dbDebug'        => true, //数据库调试开关
        'profilerDebug'  => true, //性能调试开关  数据库性能需要dbDebug开关打开
    ),

    'logFilePath' => array(
        'error'   => TMP_PATH_LOG.date('Y-m-d')."/".PROJECT_NAME.'_error.log',
        'access'  => TMP_PATH_LOG.date('Y-m-d')."/".PROJECT_NAME.'_access.log',
        'db'      => TMP_PATH_LOG.date('Y-m-d')."/".PROJECT_NAME.'_db.log',
        'profile' => TMP_PATH_LOG.date('Y-m-d')."/".PROJECT_NAME.'_profile.log',
    ),
));
