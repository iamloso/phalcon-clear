<?php
namespace PFrame\Tasks;

class FanLiTask extends \PFrame\Libs\Extensions\BaseTask
{
    public $businessType = [];

    public function testAction()
    {
        parent::runAction();
        die('aaa');
    }
}