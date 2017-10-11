<?php
/**
 * 命名空间设置
 */
$loader = new \Phalcon\Loader();

$loader->registerNamespaces(array(
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

$loader->register();

/**
 * 判断该项目是否自己注册命名空间
 */
if (file_exists(PROJECT_PATH . "/config/Loader.php")) {
    include_once PROJECT_PATH . "/config/Loader.php";
}

