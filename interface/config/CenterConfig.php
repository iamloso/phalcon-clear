<?php

/**
 * 中心业务配置
 * Class CenterConfig
 */
class CenterConfig extends \Phalcon\Config
{
    public function __construct(array $arrayConfig)
    {
        parent::__construct($arrayConfig);
    }
}

$configData = [
    /**
     * cdn版本code码
     */
    'cdnVersion'        => '201510301111',
    /**
     * 静态资源地址
     */
    'staticPath'        => 'http://dev.static-base.com/',
    /**
     * 后台地址
     */
    'adminPath'         => 'http://dev.admin-base.com/',
];

if (file_exists('CenterData.php')) {
    $configData = include_once "CenterData.php";
}


return new CenterConfig($configData);
