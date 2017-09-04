<?php
namespace PFrame\Libs\Common;

class Rsa
{

    public $pubkey;
    public $privkey;

    function __construct($source = '')
    {
        if (empty($source)) {
            $this->pubkey = file_get_contents(dirname(__FILE__) . "/appkeys/rsa_public_key.pem");
            $this->privkey = file_get_contents(dirname(__FILE__) . "/appkeys/rsa_private_key.pem");
        } else if ($source == 'APPH5') {
            $this->pubkey = file_get_contents(dirname(__FILE__) . "/appkeys/rsa_public_key.pem");
            $this->privkey = file_get_contents(dirname(__FILE__) . "/appkeys/rsa_private_key.pem");
        } else if ($source == 'WEB') {
            $this->pubkey = file_get_contents(dirname(__FILE__) . "/webkeys/rsa_public_key.pem");
            $this->privkey = file_get_contents(dirname(__FILE__) . "/webkeys/rsa_private_key.pem");
        }
        else {
            $this->pubkey = file_get_contents(dirname(__FILE__) . "/apikeys/rsa_public_key.pem");
            $this->privkey = file_get_contents(dirname(__FILE__) . "/apikeys/rsa_private_key.pem");
        }
    }

    public function encrypt($data)
    {
        if (openssl_public_encrypt($data, $encrypted, $this->pubkey))
            $data = base64_encode($encrypted);
        else
            return array();
        //throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');
        return $data;
    }

    public function decrypt($data)
    {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $this->privkey)) {
            $data = $decrypted;
        }

        else
            $data = '';
        return $data;
    }

}


?>