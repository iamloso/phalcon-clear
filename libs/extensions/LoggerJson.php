<?php
namespace PFrame\Libs\Extensions;

use Phalcon\Logger\Formatter\Json as Json;
use Phalcon\Logger\Formatter;
/**
 * 扩展Phalcon日志输出格式json类
 * Class LoggerJson
 * @package PFrame\Libs\Extensions
 */
class LoggerJson extends Json
{
    public $jsonData = [];
    /**
     * Applies a format to a message before sent it to the internal log
     *
     * @param string message
     * @param int type
     * @param int timestamp
     * @param array $context
     * @return string
     */
    public function format($message, $type, $timestamp, $context=[])
    {
        if (is_array($context)) {

            $message = $this->interpolate($message, $context);
        }
        $data = ["type"=>$this->getTypeString($type), "message"=>$message, "timestamp"=>$timestamp];
        $data = array_merge($data, $this->jsonData);

        return json_encode($data, JSON_UNESCAPED_UNICODE).PHP_EOL;
    }

    /**
     * 设置json数据
     * @param array $data
     * @return array
     */
    public function setJsonData($data = [])
    {
        if (!empty($data)) {
            $this->jsonData = $data;
        }
        return $this->jsonData;
    }
}