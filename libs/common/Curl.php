<?php 
namespace PFrame\Libs\Common;

class Curl {

    public static $errno = 0;

    public static $error = '';

    public static $httpCode = 0;

    public static $timeout = 20;

    /**
     * curl get请求
     *
     * @param string $url GET请求地址
     * @param $flag
     * @return mixed
     */
    public static function get($url,$flag = false)
    {
        if (empty($url)) {
            return false; 
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        curl_setopt($ch, CURLOPT_REFERER,'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (substr($url, 0, 5) === 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //检查证书中是否设置域名
        }

        $result = curl_exec($ch);

        self::$errno = curl_errno($ch);
        self::$error = curl_error($ch);
        self::$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if($flag) {
            return array('msg'=>$result,'code'=>self::$errno);
        }
        return $result;
    }

    /**
     * curl post 请求
     * @param string $url
     * @param array $param
     * @param bool $flag
     * @return array
     */
    public static function post($url, $param=array(), $flag = false) {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (substr($url, 0, 5) === 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //检查证书中是否设置域名
        }

        $result = curl_exec($ch);

        self::$errno = curl_errno($ch);
        self::$error = curl_error($ch);
        self::$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if($flag) {
            return array('msg'=>$result,'errno'=>self::$errno, 'error'=>self::$error, 'code'=>self::$httpCode);
        }
        return $result;
    }

}
