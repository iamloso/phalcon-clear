<?php
class TestTask extends \PFrame\Libs\Extensions\BaseTask
{
    public $businessType = [];

    public function testAction()
    {
        parent::runAction();
        die('aaa');
    }
}