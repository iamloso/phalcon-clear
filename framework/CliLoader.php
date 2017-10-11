<?php 
/**
 * 注册类自动加载器
 */
$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
    'PFrame\Tasks'                 => $Config->application->tasksDir,
    'PFrame\Libs\Models'           => $Config->application->modelsDir,
    'PFrame\Controllers'           => $Config->application->controllersDir,
    'PFrame\Libs\Services'         => $Config->application->serviceDir,
    'PFrame\Libs\Plugins'          => $Config->application->pluginsDir,
    'PFrame\Libs\Common'           => $Config->application->commonDir,
    'PFrame\Libs\Extensions'       => $Config->application->extDir,
    'PFrame\Libs\Plugins\Cache'    => $Config->application->redisCahceDir,
    'PFrame\Libs\Plugins\Db'       => $Config->application->db,
    'PFrame\Libs\Plugins\Admin'    => $Config->application->admin,
));

$loader->registerDirs(
    array(
        APP_CLI_PATH,
    )
);
$loader->register();
