<?php

use PFrame\Libs\Common\SLog;
use PFrame\Libs\Common\Common;
use PFrame\Libs\Plugins\Db\DbListener;
use PFrame\Libs\Plugins\Cache\RedisCache;
use PFrame\Libs\Plugins\NotFoundPlugin;
use PFrame\Libs\Plugins\VoltFilter;
use PFrame\Libs\Plugins\ModelListener;

$CenterConfig = include PROJECT_PATH."/config/CenterConfig.php";
$DbConfig     = include PROJECT_PATH."/config/DbConfig.php";

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new Phalcon\DI\FactoryDefault ();

$di->set ( 'dispatcher', function () use($di) {
    
    $eventsManager = new Phalcon\Events\Manager ();

    /**
     * Handle exceptions and not-found exceptions using NotFoundPlugin
     */
    $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin);
    $dispatcher = new Phalcon\Mvc\Dispatcher ();
    $dispatcher->setDefaultNamespace ( 'PFrame\Controllers' );
    $dispatcher->setEventsManager ( $eventsManager );
    return $dispatcher;
}, true );
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set ( 'url', function () use($Config) {
    $url = new Phalcon\Mvc\Url ();
    $url->setBaseUri ( $Config->application->baseUri );
    $url->setStaticBaseUri($Config->application->staticBaseUri);
    return $url;
}, true );


/*
 * common RPC
 * */
$di->set ( 'rpc', function () use($Config) {
    $rpc = new PFrame\Libs\Extensions\YarClient ();
    //extra param write here
    return $rpc;
}, true );

/**
 * Setting up the view component
 */
$di->set ( 'view', function () use($Config) {
    
    $view = new Phalcon\Mvc\View ();

    $view->setViewsDir ( $Config->application->viewsDir );
    
    $view->registerEngines ( array (
            '.volt' => function ($view, $di) use($Config) {
                
                $volt = new Phalcon\Mvc\View\Engine\Volt ( $view, $di );
                
                $volt->setOptions ( array (
                        'compiledPath' => $Config->application->cacheDir,
                        'compiledSeparator' => '_' 
                ) );
                
                if (!is_dir($Config->application->cacheDir)) {
                    Common::mkDirs($Config->application->cacheDir);
                }
                //添加自定义过滤器
                $compiler = $volt->getCompiler();
                //$filter = new VoltFilter();
                //$filter->addFilters($compiler);
                return $volt;
            },
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php' 
    ) );
    
    return $view;
}, true );

$di->set ( 'profiler', function () {
    return new \Phalcon\Db\Profiler ();
}, true );

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set ( 'db', function () use($Config, $di, $DbConfig) {
    $eventsManager = new Phalcon\Events\Manager();
    // 从di中获取共享的profiler实例
    if ($Config->application->dbDebug) {
        $profiler = $di->getProfiler ();
        $eventsManager->attach('db:beforeQuery', new DbListener($profiler));
        $eventsManager->attach('db:afterQuery', new DbListener($profiler));
    }
    $connection = new Phalcon\Db\Adapter\Pdo\Mysql ( array (
            'host' => $DbConfig->database->host,
            'port' => $DbConfig->database->port,
            'username' => $DbConfig->database->username,
            'password' => $DbConfig->database->password,
            'dbname'   => $DbConfig->database->dbname,
            "options"  => array (
                            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'  // 设置编码
                        ),
    ) );
    $connection->setEventsManager ( $eventsManager );
    return $connection;
} );
/**
 * Start the session the first time some component request the session service
 */
$di->set ( 'session', function () {
    $session = new Phalcon\Session\Adapter\Files ();
    $session->start ();
    
    return $session;
} );

/**
 * Register the flash service with custom CSS classes
 */
$di->set ( 'flash', function () {
    return new Phalcon\Flash\Session ( array (
            'error' => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice' => 'alert alert-info' 
    ) );
} );

/**
 * Router
 */
$di->set ( 'router', function () {
    return include 'Routes.php';
}, true );

/**
 * Loader
 */
$di->set ( 'loader', $loader);

/**
 * Register the configuration itself as a service
 */
$di->set ( 'config', $Config );
$di->set ( 'dbConfig', $DbConfig);
$di->set ( 'centerConfig', $CenterConfig);


$di->set ( 'slog', function () {
    $SLog = new SLog ();
    return $SLog;
} );

/**
 * redis
 */
$di->set ( 'cache', function () use($DbConfig){
    try {
        $cache = new RedisCache($DbConfig);
        return $cache;
    } catch ( \Exception $e ) {
        throw $e;
    }
} ); // end of cache

/**
 * Register Phalcon\Mvc\Model\Manager
 */
$di->setShared ( 'modelsManager', function () {
    $eventsManager = new \Phalcon\Events\Manager();
    $eventsManager->attach('model', function($event, $model){
        if (PROJECT_NAME == 'admin') {
            $ModelListener = new ModelListener();
            $ModelListener->OPChange($event, $model);
        }
    });
    //Setting a default EventsManager
    $modelsManager = new \Phalcon\Mvc\Model\Manager();
    $modelsManager->setEventsManager($eventsManager);
    return $modelsManager;
} );


