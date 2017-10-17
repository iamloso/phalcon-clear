<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Common\SLog;

class YarClient
{
    //临时存储所有的服务 将来注册到服务中心
    public $serverList  = array(
        'test_rpc'        => array('service_url'=>'http://www.phalcon.com/test/init', 'service_method'=>"testAction",'params'=>array()),
        //账户中心 eb适配器
        'openAccount'     => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"openAccount", 'params'=>array()),
        'bindBankCard'    => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"bindBankCard",'params'=>array()),
        'unBindBankCard'  => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"unBindBankCard",'params'=>array()),
        'recharge'        => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"recharge",   'params'=>array()),
        'withdraw'        => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"withdraw",   'params'=>array()),
        'freeze'          => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"freeze",     'params'=>array()),
        'transfer'        => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"transfer",   'params'=>array()),
        'lockAccount'     => array('service_url'=>'http://10.20.70.213:8080/eb',      'service_method'=>"lockAccount",'params'=>array()),
    );
    
    /**
     * yar 客户端向服务端发起同步请求
     * @param $serviceName 服务名称
     * @param $params      请求传输数据
     * @return mixed
     */
    public function request($serviceName, $params) {
        $this->rpcLog("rpc request start: service name =".$serviceName ,array("service_name"=>$serviceName,"params"=>json_encode($params)));
        $serviceInfo = $this->getServiceConfig($serviceName);
        //请求透传
        $params["trace_client_ip"] = "";
        $params["trace_id"]        = "";
        
        //增加性能日志
        list($usec, $sec) = explode(" ", microtime());
        $timeStart = (float)$usec + (float)$sec;
        
        try{
            $client   = new \Yar_Client($serviceInfo["service_url"]);
            $method   = $serviceInfo["service_method"];
            $result   = $client->$method($params);
        }catch(\Exception $e){
            $this->rpcLog('rpc request error: service_name:'.$serviceName.', exception info:'.$e->getMessage(),array(), SLog::ERROR);
            $result = array();
        }
        
        list($usec2, $sec2) = explode(" ", microtime());
        $timeEnd = (float)$usec2 + (float)$sec2;
        $this->rpcLog("rpc request end: service name =".$serviceName,array("service_name"=>$serviceName,"time_cost" => $timeEnd-$timeStart) );
        
        return $result;
    }

    /**
     * 获取服务配置
     * @param $serviceName 服务名称
     * @return array
     */
    private function getServiceConfig( $serviceName) {
        $serviceInfo  = $this->serverList[$serviceName];
        return $serviceInfo;
    }
    
    /**
     * yar client 层日志
     * @param string $log
     * @param array  $extraParams
     * @param $logType
     * @return null
     */
    private function rpcLog($log,$extraParams = array(),$logType=SLog::INFO){
        $logPath   = TMP_PATH_LOG.'/yar_client_log/rpc.log';
        SLog::$jsonData = $extraParams;
        SLog::writeLog($log,$logType,$logPath);
    }
    
    
}

