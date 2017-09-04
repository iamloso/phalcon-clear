<?php
/**
 * 默认路由配置
 */
$router = new Phalcon\Mvc\Router(false);


$router->add('/', array(
    'controller' => 'index',
    'action'     => 'index'
));

$router->add("/:controller/:action/:params", array(
    "controller" => 1,
    "action"     => 2,
    "params"     => 3,
));

return $router;
