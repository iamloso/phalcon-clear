<?php
namespace PFrame\Libs\Services;

/**
 * Service初始化,创建实例.
 *
 * Class InitializeService
 * @package DBank\Dq\Services
 * @auth LuoJinLong
 */
trait InitializeService
{
    private static $_instance = array();

    /**
     * 创建serviceName完全限定名称的实例.
     * @param $serviceName
     * @return object
     */
    public static function getInstance($serviceName)
    {
        $serviceNameSpace = __NAMESPACE__ . '\\' . $serviceName;

        if (!isset(self::$_instance[$serviceName]) || !(self::$_instance[$serviceName] instanceof $serviceNameSpace)) {

            self::$_instance[$serviceName] = new $serviceNameSpace();
        }
        return self::$_instance[$serviceName];
    }
}
