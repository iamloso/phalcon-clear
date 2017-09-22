<?php
namespace PFrame\Libs\Models;
use PFrame\Libs\Extensions\InterfaceModel;
/**
 * Created by PhpStorm.
 * User: loso
 * Date: 17-9-18
 * Time: 下午6:23
 */
class Borrow extends InterfaceModel
{
    public function initialize()
    {

        $this->setSource($this->getSource());
        parent::initialize();
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'dq_borrow';
    }
}