<?php
namespace PFrame\Libs\Plugins\Admin;

/**
 * Elements
 *
 * Helps to build UI elements for the application
 */
class Elements extends \Phalcon\Mvc\User\Component
{

    private $menuTop = array(
        array("lable" => "首页", "url" => "/index/info", 'visible' => array("*")),
    );

    private $menuTopRightLogin = array(
        array("lable" => "退出", "url" => "/session/end", 'visible' => ""),
    );

    private $menuTopRightNologin = array(
        array("lable" => "登陆", "url" => "/session/start", 'visible' => ""),
    );

    private $menuLeft = array(
        "首页" => array(
            "首页" => array(
                array("lable" => "首页", "url" => "/index/home", 'visible' => array("*")),
            ),
        ),
    );

    public function GetMenuTop($roles = '')
    {
        return $this->menuTop;
    }

    public function GetMenuTopRight($login_status = false)
    {
        if ($login_status) {
            return $this->menuTopRightLogin;
        } else {
            return $this->menuTopRightNologin;
        }

    }

    public function GetMenuLeft()
    {
        return $this->menuLeft;
    }

    public function getMenuLeftIcon()
    {
        return $this->menuLeftIcon;
    }

    public function parserMenuTop($menuTop, $roles)
    {
        foreach ($menuTop as $key => $row) {
            if (array_diff(array("*"), $row["visible"]) != false && (array_intersect($roles, $row["visible"]) == false)) {
                unset($menuTop[$key]);
            }
        }
        return $menuTop;
    }

    public function parserMenuLeft($menuLeft, $roles)
    {
        foreach ($menuLeft as $group => &$items) {
            foreach ($items as $key => &$item) {
                if (array_diff(array("*"), $item["visible"]) != false && (array_intersect($roles, $item["visible"]) == false)) {
                    unset($items[$key]);
                }
            }
            if (empty($items)) {
                unset($menuLeft[$group]);
            }
        }
        return $menuLeft;
    }

}
