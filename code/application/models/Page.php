<?php
/**
 * @author      :   HoaiTN
 * @name        :   Model Page
 * @version     :   20130502
 * @copyright   :   My company
 * @todo        :   Page model
 */

class Page implements Zend_Paginator_Adapter_Interface
{
    protected $_data;
    protected $_total;
	
    public function __construct($data, $total)
    {
        $this->_data = $data;
        $this->_total = $total;
    }
    
	
    /**
     * @implements Zend_Paginator_Adapter_Interface
     * @return integer
     */
    public function count()
    {   
	  return $this->_total;	
    }

    
    public function getItems($offset, $itemCountPerPage)
    {
        $results 	= $this->_data;
       
		return $results;
    }
    
}

?>
