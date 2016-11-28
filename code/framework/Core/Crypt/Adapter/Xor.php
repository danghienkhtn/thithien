<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Crypt_Adapter_Xor
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to crypt
 */
class Core_Crypt_Adapter_Xor extends Core_Crypt_Adapter_Abstract
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
     * Gen key private
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public function keyED($string,$keyphrase)
    {
        $string = (string)$string;
        $keyphraseLength = strlen($keyphrase);
        $stringLength = strlen($string);
        for($i = 0; $i < $stringLength; $i++)
        {
            $rPos = $i % $keyphraseLength;
            $r = ord($string[$i]) ^ ord($keyphrase[$rPos]);
            $string[$i] = chr($r);
        }
        return $string;
    }

    /**
     * encrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public function encode($string,$keyphrase)
    {        
        $string = $this->keyED($string, $keyphrase);
        $string = base64_encode($string);
        return $string;
    }

    /**
     * decrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     * @return <string>
     */
    public function decode($string,$keyphrase)
    {        
        $string = base64_decode($string);
        $string = $this->keyED($string, $keyphrase);
        return $string;
    }
}

