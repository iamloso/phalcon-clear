<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Common;

class YarClient
{
    /**
     * yar 客户端向服务端发起同步请求
     * @param $serviceMerchant 服务商
     * @param $serviceName 服务名称
     * @param $params      请求传输数据
     * @return mixed
     */
    public function request($serviceMerchant, $serviceName, $params) {
        $this->clientLog('yar client start request, service_merchant:'.$serviceMerchant.', service_name:'.$serviceName.', data:'.json_encode($params, JSON_UNESCAPED_UNICODE));
        $serviceInfo = $this->getServiceConfig($serviceMerchant, $serviceName);
        if (!empty($params)) {
            $serviceInfo['params'] = $params;
        }
        try{
            $pathInfo = pathinfo($serviceInfo["service_path"]);
            $serviceInfo['params']['controller'] = $pathInfo['basename'];
            $serviceInfo['params']['action']     = $pathInfo['filename'];
            $method   = $pathInfo['filename'].'Action';
            $servicePath = $pathInfo['dirname'].'/init';
            $client   = new \Yar_Client($servicePath);
            $result   = $client->$method($serviceInfo['params']);
        }catch(\Exception $e){
            $this->clientLog('yar client request error, service_merchant:'.$serviceMerchant.', service_name:'.$serviceName.', exception info:'.$e->getMessage(), 'ERROR');
            $result = array();
        }
        return $result;
    }

    /**
     * 获取服务配置
     * @param $serviceMerchant 服务商
     * @param $serviceName 服务名称
     * @return array
     */
    public function getServiceConfig($serviceMerchant, $serviceName) {
        $serviceConfig = include_once "ServiceConfig.php";
        $serviceInfo  = $serviceConfig[$serviceMerchant][$serviceName];
        return $serviceInfo;
    }

    /**
     * yar client 层日志
     * @param string $log
     * @param string $logType
     * @return null
     */
    public function clientLog($log,$logType='INFO'){
        if( $logType == 'ERROR' ) {
            $logType = Common\SLog::ERROR;
        } elseif ( $logType == 'WARNING' ) {
            $logType = Common\SLog::WARNING;
        } elseif ( $logType == 'DEBUG' ) {
            $logType = Common\SLog::DEBUG;
        } elseif ( $logType == 'CRITICAL' ) {
            $logType = Common\SLog::CRITICAL;
        } else {
            $logType = Common\SLog::INFO;
        }
        $className = str_replace("\\", "-", get_class($this)); //get_called_class()
        $logPath   = TMP_PATH_LOG.date('Y-m-d').'/yar_client_log/'.$className.'.log';
        Common\SLog::writeLog($log,$logType,$logPath);
    }
}

