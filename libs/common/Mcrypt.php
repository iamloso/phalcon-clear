<?php
namespace PFrame\Libs\Common;

use Phalcon\Crypt;
/**
 * 加密解密算法
 * @author p2pyun
 *
 */
class Mcrypt {
    /**
     * 加解密时需要的key
     * @var unknown
     */
    const CRYPT_KEY = '2c0346e6ca8c240d018776039a76e379';
    
    /**
     * 加密解密对象
     * @var unknown
     */
    public $crypt;
    
    public function __construct() {
        $this->crypt = new Crypt();
    }
    /**
     * 加密
     * @param unknown $text
     * @param unknown $key
     * @return string
     */
    public function encrypt($text, $key = self::CRYPT_KEY) {
        return $this->crypt->encrypt($text, $key);
    }
    /**
     * 解密
     * @param unknown $text
     * @param unknown $key
     * @return mixed
     */
    public function decrypt($text, $key = self::CRYPT_KEY) {
        try{
            $res =$this->crypt->decrypt($text, $key);
            return $res;
        
        } catch (\Exception $e) {
            SLog::writeLog($e, SLog::ERROR);
            return false;
        }
    }
    /**
     * base64 加密结果
     * @param unknown $text
     * @param unknown $key
     * @return string
     */
    public function encryptBase64($text, $key = self::CRYPT_KEY) {
        $res = $this->crypt->encryptBase64($text, $key);
        $res = str_replace('+', ')', str_replace('/', '(',$res));
        return $res;
    }
    /**
     * base64 解密结果
     * @param unknown $text
     * @param unknown $key
     * @return mixed
     */
    public function decryptBase64($text, $key = self::CRYPT_KEY) {
        try{
            $text = str_replace(')','+',str_replace('(','/',$text));
            $res = $this->crypt->decryptBase64($text, $key);
            return $res;
        } catch (\Exception $e) {
            SLog::writeLog($e, SLog::ERROR);
            return false;
        }
    }
    
    /**
     * 设置key
     * @param unknown $key
     * @return string
     */
    public function setKey($key) {
        $this->$key = md5('DQCENTER'.time().$key);
        return $this->$key;
    }
}