<?php
namespace Ode;
class Encrypt {
    private $handle;
    private $privateKey;
    private $publicKey;
    private $data;
    private $encrypted;
    private $encoded;
    
    const EXCEPTION_NO_OPENSSL = 1645001;
    
    public function __construct() {
        if(!function_exists('openssl_pkey_new')) {
            throw new \Exception("The function openssl_pkey_new does not exist. PHP openssl extension is not available.", self::EXCEPTION_NO_OPENSSL);
        }
        
        $this->handle = openssl_pkey_new(array(
            'digest_alg' => 'sha1',
            'private_key_bits' => 384,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        ));
    }
    
    public function setPrivateKey($string = false) {
        $privateKey = $string;
        
        if($string === false) {
            openssl_pkey_export($this->handle, $privateKey);
            $privateKey = $privateKey;
        }
        
        $this->privateKey = $privateKey;
    }
    
    public function getPrivateKey() {
        return $this->privateKey;
    }
    
    public function setPublicKey($string = false) {
        $publicKey = $string;
        
        if($string === false) {
            $publicKey = openssl_pkey_get_details($this->handle);
            $publicKey = $publicKey['key'];
        }
        
        $this->publicKey = $publicKey;
    }
    
    public function getPublicKey() {
        return $this->publicKey;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function setEncrypted() {
        openssl_public_encrypt($this->getData(), $encrypted, $this->getPublicKey());
        
        $this->encrypted = $encrypted;
        
        $this->setEncoded(base64_encode($this->getEncrypted()));
    }
    
    public function getEncrypted() {
        return $this->encrypted;
    }
    
    public function setEncoded($data) {
        $this->encoded = $data;
    }
    
    public function getEncoded() {
        return $this->encoded;
    }
    
    public function encrypt($data, $pubKey) {
        $this->setPublicKey($pubKey);
        
        $this->setEncrypted();
    }
    
    public function decrypt($enc, $privKey) {
        openssl_private_decrypt($enc, $decrypted, $this->getPrivateKey());
        
        return $decrypted;
    }
}

// test
/*$enc = new Ode_Encrypt();
$enc->setPrivateKey();
echo "Private key: " . $enc->getPrivateKey();
$enc->setPublicKey();
echo "<br />Public key: " . $enc->getPublicKey();
$enc->setData('1234');
echo "<br />Data: " . $enc->getData();
$enc->setEncrypted();
echo "<br />Encoded: " . $enc->getEncoded();*/

