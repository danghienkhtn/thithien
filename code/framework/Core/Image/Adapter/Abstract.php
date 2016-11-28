<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Image
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to images
 */
abstract class Core_Image_Adapter_Abstract
{
    /** define constant for image section */    
    const IMAGE_ITEM_FULL_SIZE          = "fullsize";
    const IMAGE_ITEM_SQUARE_SIZE        = "squaresize";
    const IMAGE_ITEM_ALLOW_SIZE         = "alowsize";
    const IMAGE_ITEM_BACKGROUND         = "background";
    const IMAGE_ITEM_DEFAULT_TYPE       = "ext";
    const IMAGE_ITEM_QUALITY	        = "quality";

    /* Options */
    protected $options = array();

    /**
     * Get options child
     * @param <array> $options
     * @return <array>
     */
    protected function getOptions($options)
    {
        return $options[$options['adapter']];
    }

    /**
     * Set options
     * @param <array> $options
     */
    protected function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Parse background
     * @param <string> $background
     * @return <string>
     */
    protected function parseBackground($background)
    {
    	list($r,$g,$b) = explode(",",$background);
    	return $this->rgbHex($r,$g,$b);
    }

    /**
     * Get hex data
     * @param <string> $r
     * @param <string> $g
     * @param <string> $b
     * @return <string>
     */
    protected function rgbHex($r,$g,$b)
    {
    	return sprintf("#%02X%02X%02X",$r,$g,$b);
    }

    /**
     * Resize image
     * @param <string> $binary_source
     * @param <int> $newwidth
     * @param string $background
     * @param <string> $ext
     * @return <string>
     */
    public function resize(&$binary_source,$newwidth,$background = null,$ext = "jpg")
    {
        //Check background
        if(empty($background))
        {            
            $background = $this->options[self::IMAGE_ITEM_BACKGROUND];
        }

        //Resize binary
        return $this->_resize($binary_source,$newwidth,$background,$ext);
    }

    /**
     * Resize map
     */
    public abstract function _resize($binary_source,$newwidth,$background,$ext);

    /**
     * Resize but keep from blob
     */
    public abstract function resizeKeepWithFromBlob(&$binary_source,$newwidth,$ext);

    /**
     * Resize square from blob
     */
    public abstract function resizeInSquareFromBlob(&$binary_source,$newwidth,$background,$ext);

    /**
     * Resize from file
     */
    public abstract function resizeKeepWithFromFile($src_name,$dest_name,$newwidth,$newheight,$ext);

    /**
     * Resize square from file
     */
    public abstract function resizeInSquareFromFile($src_name,$dest_name,$newwidth,$newheight,$background,$ext);

    /**
     * Resize square from blob to file     
     */
    public abstract function resizeInSquareFromBlobToFile(&$binary_source,$newwidth,$newheight,$background,$outFile);

    /**
     * Resize from blob to file     
     */
    public abstract function resizeKeepWithFromBlobToFile(&$binary_source,$newwidth,$newheight,$outFile); 
}