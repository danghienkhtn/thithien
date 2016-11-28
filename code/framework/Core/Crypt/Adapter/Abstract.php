<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Crypt_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to crypt
 */
abstract class Core_Crypt_Adapter_Abstract
{
    /**
     * Gen key private
     * @param <string> $string
     * @param <string> $keyphrase
     */
    abstract protected function keyED($string,$keyphrase);

    /**
     * encrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     */
    abstract protected function encode($string,$keyphrase);

    /**
     * decrypt string
     * @param <string> $string
     * @param <string> $keyphrase
     */
    abstract protected function decode($string,$keyphrase);
}

