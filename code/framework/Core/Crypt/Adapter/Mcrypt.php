<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Crypt_Adapter_Mcrypt
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to crypt
 */
class Core_Crypt_Adapter_Mcrypt extends Core_Crypt_Adapter_Abstract
{
    /**
    * Constructor
    *
    */
    public function __construct(){}

    /**
    * Destructor
    */
    public function __destruct(){}
    
    /**
     * Hex from Bin
     * @param <string> $data
     * @return <string> 
     */
    private function hexFromBin($data)
    {
        return bin2hex($data);
    }

    /**
     * Bin from Hex
     * @param <string> $data
     * @return <string> 
     */
    private function binFromHex($data)
    {
        $len = strlen($data);
        return pack("H" . $len, $data);
    }
    
    /**
     * Gen key private
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public function keyED($string,$keyphrase)
    {
        return $keyphrase;
    }

    /**
     * encrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public function encode($string, $keyphrase)
    {  
        $text = $string;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_3DES, $keyphrase, $text, MCRYPT_MODE_ECB, $iv);
        return $this->hexFromBin($crypttext);
    }

    /**
     * decrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public function decode($string, $keyphrase)
    {        
        $crypttext = $this->binFromHex($string); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_3DES, $keyphrase, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
}

