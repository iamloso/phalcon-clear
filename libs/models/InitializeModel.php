<?php
namespace PFrame\Libs\Models;
/**
 * Models初始化,创建实例.
 *
 * Class InitializeService
 * @package PFrame\Libs\Models
 * @auth LuoJinLong
 */
trait InitializeModel
{
    private static $_instance = array();

    /**
     * 创建modelsName完全限定名称的实例.
     *
     * @param $modelName
     * @return object
     */
    public static function getInstance($modelName)
    {
        $modelNameSpace = __NAMESPACE__ . '\\' . $modelName;

        if (!isset(self::$_instance[$modelName]) || !(self::$_instance[$modelName] instanceof $modelNameSpace)) {

            self::$_instance[$modelName] = new $modelNameSpace();
        }
        return self::$_instance[$modelName];
    }
}
