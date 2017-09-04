<?php
namespace PFrame\Libs\Common;

/**
 * 通用类：常用函数定义
 *  @author Dqcenter
 */
class Common
{

    /**
     * 获取用户ip
     * @return ip <string>
     */
    public static function getClientIp()
    {
        if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
            $ip = getenv ( "HTTP_CLIENT_IP" );
        else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
            $ip = getenv ( "HTTP_X_FORWARDED_FOR" );
        else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
            $ip = getenv ( "REMOTE_ADDR" );
        else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
            $ip = $_SERVER ['REMOTE_ADDR'];
        else
            $ip = "unknown";
        return substr($ip,0,15);
    }
    
    /**
     * 获取GMTime
     * @return number
     */
    public static function getGmtime()
    {
        return (time() - date('Z'));
    }

    /**
     * 错误信息提示
     * @param unknown $message 消息
     * @param string $url 空：不跳转;非空：跳转到指定页面
     */
    
    public static function msgOut($message, $url="")
    {
        header("Content-Type: text/html;charset=utf-8");
        if($url == "") {
            $url = $_SERVER['HTTP_REFERER'];
        }
        echo "<script>alert('" .str_replace('\'', '`', $message). "');window" . ".location.href='" . $url . "';</script>";
        exit;
    }
    
    /**
     * 执行成功，页面跳转
     * @param string $url
     */
    public static function successOut($url="")
    {
        header("Content-Type: text/html;charset=utf-8");
        if($url == "") {
            $url = $_SERVER['HTTP_REFERER'];
        }
        echo "<script>window" . ".location.href='" . $url . "';</script>";
        exit;
    }

    /**
     * 获取base uri
     */
    public static function getBaseUri()
    {
        $url = new \Phalcon\Mvc\Url();
        return  $url->getBaseUri();
    }

    /**
     * 处理异常
     * @param unknown $debug
     * @param unknown $e
     */
    public static function processException($debug, $e)
    {
        /**
         * Show an static error page
         */
        if($debug != true) {
            $response = new \Phalcon\Http\Response();
            $response->redirect('500.html');
            $response->send();
        } else {
            echo $e->getMessage();
        }
    }

    /**
     * 创建多级目录
     * @param string $dir
     * @param int $mode
     * @return boolean
     */
    public static function mkDirs($dir,$mode=0777)
    {
        if(is_dir($dir)||@mkdir($dir,$mode)){
            return true;
        }
        if(!self::mkDirs(dirname($dir),$mode)){
            return false;
        }
        return @mkdir($dir,$mode);
    }
    
    /**
     * 格式化时间函数
     * @param unknown $time
     * @param string $format
     * @return NULL|string
     */
    public static function dateFormat($time, $format='Y-m-d H:i')
    {
        if (empty($time)) {
            return null;
        }
        return date($format,$time);
    }

    /**
     * 判断是否手机端访问
     * @return bool
     */
    public static function isMobile()
    {
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array ('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-',
                'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android',
                'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone',
                'cldc', 'midp', 'wap', 'mobile'
            );
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 是否微信入口
     * @return bool
     */
    public static function isWeixin()
    {
        if (preg_match("/MicroMessenger/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 格式化钱 ￥2,000
     * @param unknown $price
     * @param bool $pre
     * @param int $decimal
     * @return string
     */
    public static function formatPrice($price, $pre = true, $decimal = 2)
    {
        if($pre) {
            return "￥".number_format($price, $decimal);
        } else {
            return number_format($price, $decimal);
        }
    }

    /**
     * 循环生成还款期限列表
     */
    public static function getRepayTimeMonth($start=2, $end=36, $step=1, $period="个月")
    {
        $res = array();
        for ($i=$start; $i<=$end; $i+=$step) {
            $res[$i] = $i . $period;
        }
        return $res;
    }
    
    /**
     * 字符串截取
     * @param unknown $string
     * @param unknown $len
     * @param string $etc
     * @return string
     */
    public static function shortString($string, $len, $etc = '')
    {
        $encoding = 'UTF-8';
        if (mb_strlen($string, $encoding) > $len) {
            $string = mb_substr($string, 0, $len, $encoding). $etc;
        }
        return $string;
    }
    /**
     *  输出数据到cvs文件
     *
     * @param $header array cvs文件的标题
     * @param $data array 输出的内容，是个二围数组
     * @param $out string 输出的cvs文件的名称,不填默认以时间(Y-m-d H:i:s)作为默认的文件名
     *
     * @return null 直接输出cvs文件到本定
     *
     * @static
     * ```
     * Usage:
     *     $header = array('name', 'age');
     *     $data   = array(array('jason', 18). array('mary', 18));
     *     data2cvs($header, $data);
     *     exit();  //后面一定要带上exit
     * ```
     */
    public static function data2cvs($header=array(), $data=array(), $out='')
    {
        if (empty($out)) {
            $out = date('Y-m-d') . '.' . 'csv';
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $out . '"');
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output', 'a');

        $headerField = array();
        foreach ($header as $i=>$v) {
            $headerField[$i] = iconv('utf-8', 'gbk', $v);
        }
        fputcsv($fp, $headerField, ",");

        $dataField = array();
        $limit = 500;
        $counter = 0;
        foreach ($data as $item) {
            ++$counter;
            if ($counter == $limit) {
                ob_flush();
                flush();

                $counter = 0;
            }
            foreach ($item as $k=>$v) {
                $dataField[$k] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($fp, $dataField, ",");
        }

        ob_end_flush();
        flush();
    }

    /**
     * @param $id
     * @return string
     */
    public static function idnoFormat($id){
        return self::bankidFormat($id);
    }

    /**
     * 格式化 银行卡号
     * @param unknown $id
     * @return string
     */
    public static function bankidFormat($id){
        if(!$id){
            return '';
        }
        $id = trim($id);
        return substr($id, 0,4).str_repeat("*", strlen($id)-8).substr($id, -4);
    }
    /**
     * 手机号码格式化
     * @param unknown $id
     * @return string
     */
    public static function moblieFormat($id){
        if(!$id){
            return '';
        }
        return substr($id, 0,6).str_repeat("*", strlen($id)-7).substr($id, -1);
    }
}

