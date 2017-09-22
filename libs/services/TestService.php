<?php
namespace PFrame\Libs\Services;

use PFrame\Libs\Extensions\BaseService;
use PFrame\Libs\Models\InitializeModel as IntMs;
use PFrame\Libs\Services\InitializeService as IntSvs;

class TestService extends BaseService
{
    public  function test($data = array())
    {
        $result = IntMs::getInstance('Borrow')->getOneData(['conditions' => 'borrow_id=251']);
        return $result;
    }
}
