<?php
namespace PFrame\Libs\Common;
/**
 * 通用share trait
 * @author Dqcenter
 */
trait ShareTrait
{
    public $aesKey;


    public function setAesKey()
    {
        $this->aesKey = md5(time());
    }

    public function getAesKey()
    {
        return $this->aesKey;
    }

    /**
     * 创建加密数据
     * @param $data
     * @param string $source
     * @return mixed
     */
    public function createDataTm($data, $source = 'inside')
    {
        $dataStr = http_build_query($data);
        $this->aesKey = md5(time());

        $rsa = new Rsa($source);
        $result['tm']  = $rsa->encrypt($this->aesKey);

        $aes = new CryptAES($this->aesKey);
        $result['data']= $aes->encrypt($dataStr);
        return $result;
    }

    /**
     * 解密数据
     * @param $data
     * @param $aesKey
     * @return mixed
     */
    public function decryptData($data, $aesKey)
    {
        $aes = new CryptAES($aesKey);
        $result = $aes->decrypt($data);
        return $result;
    }

    /**
     * 执行短信发送
     * @param $userId
     * @param $mobile
     * @param $content
     * @param string $tpl
     * @param bool $isDirect
     * @param int $isMarketSms
     * @return array
     */
    public function smsSend($userId, $mobile, $content,$tpl = '', $isDirect = true, $isMarketSms = 0) {
        $this->serviceTraceLog(__CLASS__,__FUNCTION__,__LINE__,func_get_args());
        $sendResult = $this->icloud->rpc("Sms","send",array(
            "user_id" => $userId,
            "channel" => 0,
            "mobile" => $mobile,
            "title" => '',
            "content" => $content,
            "is_direct" => $isDirect,
            "is_market_sms" => $isMarketSms,
            "tpl" => $tpl,
        ));

        $this->serviceLog("rpc sms send result : ".var_export($sendResult, true) ,'INFO');
        if(!empty($sendResult['data'])){
            return $sendResult['data'];
        } else {
            return array('status'=> -1, 'msg'=>'短信发送失败');
        }
    }

    /**
     * 创建签名
     * @param $data 签名数据
     * @param $secret 签名秘钥
     * @param $skipData array 不参与生产签名 key
     * @return mixed
     */
    public function createSign($data, $secret, $skipData = array()){
        if (empty($secret)) {
            return false;
        }
        $signStr  = '';
        ksort($data);
        foreach($data as $key => $value) {
            if ($value == '') {
                continue;
            }

            if (!empty($skipData) && in_array($key, $skipData)) {
                continue;
            }
            $signStr .= "$key=$value&";
        }

        $signStr = trim($signStr, '&')."&key=".$secret;
        $privateSign = strtoupper(md5($signStr));
        return $privateSign;
    }
}
