<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Server_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to utility
 */
abstract class Core_Server_Adapter_Abstract
{
	protected $_class='';
	protected $_method='';	
	protected static $magicMethods = array(
        '__construct' => 1,
        '__destruct' => 1,
        '__get' => 1,
        '__set' => 1,
        '__call' => 1,
        '__sleep' => 1,
        '__wakeup' => 1,
        '__isset' => 1,
        '__unset' => 1,
        '__tostring' => 1,
        '__clone' => 1,
        '__set_state' => 1,
    );
        
    /**
     * Constructor
     *
     */
    public function __construct()
    {
    	$this->_class = '';
		$this->_method = '';
    }
	
    /**
     * Destructor
     *
     */
	public function __destruct()
	{
		$this->_class = '';
		$this->_method = '';
	}	
    
	/**
     * Set class to handle
     *
     * @param string $className
     */
	public function setClass($className)
	{
		$this->_class = $className;
	}
	
	/**
     * Lowercase a string
     *
     * Lowercase's a string by reference
     *
     * @param string $value
     * @param string $key
     * @return string Lower cased string
     */
	public static function lowerCase(&$value, &$key)
	{
		return $value = strtolower($value);
	}
	
	/**
	 * Handle class
	 * <param> $request
	 */
	abstract protected function handle($request);
}

