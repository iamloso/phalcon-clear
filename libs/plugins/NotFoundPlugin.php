<?php
namespace PFrame\Libs\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use PFrame\Libs\Common\SLog;
    
/**
* NotFoundPlugin
*
* Handles not-found controller/actions
*/
class NotFoundPlugin extends Plugin
{
    /**
    * This action is executed before execute any action in the application
    *
    * @param Event $event
    * @param MvcDispatcher $dispatcher
    * @param \Exception $exception
    * @return bool
    */
    public function beforeException(Event $event, MvcDispatcher $dispatcher, \Exception $exception)
    {
        if ($exception instanceof DispatcherException) {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                $dispatcher->forward(array( 'controller' => 'errors', 'action' => 'show404' ));
                error_log($exception->getMessage());
                return false;
            }
        }
        SLog::writeLog($exception->getMessage(), SLog::ERROR);
        error_log($exception->getMessage());
        $dispatcher->forward(array( 'controller' => 'errors', 'action' => 'show500' ));
        return false;
    }
}