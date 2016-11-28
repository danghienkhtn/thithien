<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Image_Adapter_Gd2
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to images
 */
class Core_Image_Adapter_Gd2 extends Core_Image_Adapter_Abstract
{
    /**
    * Constructor
    *
    */
    public function __construct($options = array())
    {
        //Get options child
        $options = $this->getOptions($options);

        //Check fullsize
        if(empty($options[parent::IMAGE_ITEM_FULL_SIZE]))
        {
            $options[parent::IMAGE_ITEM_FULL_SIZE] = 200;
        }

        //Check squaresize
        if(empty($options[parent::IMAGE_ITEM_SQUARE_SIZE]))
        {
            $options[parent::IMAGE_ITEM_SQUARE_SIZE] = 180;
        }

        //Check alowsize
        if(empty($options[parent::IMAGE_ITEM_ALLOW_SIZE]))
        {
            $options[parent::IMAGE_ITEM_ALLOW_SIZE] = "660,200,150,130,100,50,32";
        }

        //Check background
        if(empty($options[parent::IMAGE_ITEM_BACKGROUND]))
        {
            $options[parent::IMAGE_ITEM_BACKGROUND] = "255,255,255";
        }

        //Check type
        if(empty($options[parent::IMAGE_ITEM_DEFAULT_TYPE]))
        {
            $options[parent::IMAGE_ITEM_DEFAULT_TYPE] = "jpg";
        }

        //Check quality
        if(empty($options[parent::IMAGE_ITEM_QUALITY]))
        {
            $options[parent::IMAGE_ITEM_QUALITY] = 95;
        }

        //Set options
        $this->setOptions($options);
    }

    /**
    * Destructor
    *
    */
    public function __destruct()
    {
        //Unset options
        unset($this->options);
    }

    /**
     * Create source image
     * @param <string> $src_name
     * @param <string> $extension
     * @return <image resource>
     */
    private function createSourceImage($src_name,$extension)
    {
        switch($extension)
        {
            case "jpg" :
            case "jpeg" :
                return imagecreatefromjpeg($src_name);
                break;
            case "bmp" :
                return imagecreatefromwbmp($src_name);
                break;
            case "gif" :
                return imagecreatefromgif($src_name);
                break;
            case "png":
                return imagecreatefrompng($src_name);
                break;
            default :
                return false;
        }
        return true;
    }

    /**
     * Resize image
     * @param <string> $binary_source
     * @param <int> $newwidth
     * @param string $background
     * @param <string> $ext
     * @return <string>
     */
    public function  _resize($binary_source, $newwidth, $background, $ext)
    {
        return $this->resizeInSquareFromBlob($binary_source,$newwidth,$background,$ext);
    }

    /**
     * Resize square from blob
     * @param <string> $binary_source
     * @param <int> $newwidth
     * @param <string> $background
     * @param <string> $ext
     * @return <string>
     */
    public function resizeInSquareFromBlob(&$binary_source,$newwidth,$background,$ext)
    {
        //Create image source from string
        $source_image = imagecreatefromstring($binary_source);

        //Check source
        if(!$source_image)
        {
            return false;
        }

        //Check background
        if(empty($background))
        {
            $background = $this->options[self::IMAGE_ITEM_BACKGROUND];
        }

        //Get old size
        $old_x = imagesx($source_image);
        $old_y = imagesy($source_image);

        //Check have correct site
        if($old_x <= $newwidth && $old_y <= $newwidth)
        {
            //Destroy image source
            imagedestroy($source_image);

            //Return blob image data
            return $binary_source;
        }

        //Resize keep width
        if($old_x < $newwidth)
        {
            $newheight = $newwidth;
            $newwidth = $old_x;
            $dest_image = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $newwidth , $newheight, $newwidth, $newheight);
        }
        else
        {
            $newheight = ceil((($newwidth*$old_y)/$old_x));
            $dest_image = imagecreatetruecolor($newwidth, $newwidth);
            list($r,$g,$b) = explode(",",$background);
            $color= imagecolorallocate($dest_image, $r, $g, $b);
            $move_space =0;
            if($old_x > $old_y )
            {
                $move_space = ceil(($newwidth - $newheight)/2);
            }            
            imagefill($dest_image,0,0,$color);
            imagecopyresampled($dest_image, $source_image, 0, $move_space, 0, 0, $newwidth , $newheight, $old_x, $old_y);
        }

        //Get binary string
        ob_start();
        imagejpeg($dest_image,null,$this->options[parent::IMAGE_ITEM_QUALITY]);
        $return = ob_get_contents();
        ob_end_clean();

        //Destroy images
        imagedestroy($dest_image);
        imagedestroy($source_image);

        //Return blob image data
        return  $return;
    }

    /**
     * Resize but keep from blob
     * @param <string> $binary_source
     * @param <int> $newwidth
     * @param <string> $ext
     * @return <string>
     */
    public function resizeKeepWithFromBlob(&$binary_source,$newwidth,$ext)
    {
        //Create image source from string
        $source_image = imagecreatefromstring($binary_source);

        //Check source
        if(!$source_image)
        {
            return false;
        }
        
        //Get old size
        $old_x = imagesx($source_image);
        $old_y = imagesy($source_image);
        
        //Check have correct site
        if($old_x <= $newwidth)
        {
            //Destroy image source
            imagedestroy($source_image);

            //Return blob image data
            return $binary_source;
        }

        //Resize keep width
        $newheight = ceil((($newwidth*$old_y)/$old_x));
        $dest_image = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $newwidth , $newheight, $old_x, $old_y);
        
        //Get binary string
        ob_start();
        imagejpeg($dest_image,null,$this->options[parent::IMAGE_ITEM_QUALITY]);
        $return = ob_get_contents();
        ob_end_clean();

        //Destroy images
        imagedestroy($dest_image);
        imagedestroy($source_image);

        //Return blob image data
        return  $return;
    }

    /**
     * Resize square from blob to file
     * @modify nhuantp@fpt.net
     * @param <string> $binary_source
     * @param <int> $newwidth
     * @param <string> $background
     * @param <string> $outFile
     * @return <boolean>
     */
    public function resizeInSquareFromBlobToFile(&$binary_source,$newwidth,$newheight,$background,$outFile)
    {
       //Create image source from string
        $source_image = imagecreatefromstring($binary_source);

        //Check source
        if(!$source_image)
        {
            return false;
        }

        //Check background
        if(empty($background))
        {
            $background = $this->options[self::IMAGE_ITEM_BACKGROUND];
        }

        //Get old size
        $old_x = imagesx($source_image);
        $old_y = imagesy($source_image);

        //Check have correct site
        if($old_x <= $newwidth && $old_y <= $newwidth)
        {
            //Destroy image source
            imagedestroy($source_image);

            //Return image file
            return file_put_contents($outFile,$binary_source);
        }

        //Resize keep width
        if($old_x < $newwidth)
        {
            $newheight = $newwidth;
            $newwidth = $old_x;
            $dest_image = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $newwidth , $newheight, $newwidth, $newheight);
        }
        else
        {
            $newheight = ceil((($newwidth*$old_y)/$old_x));
            $dest_image = imagecreatetruecolor($newwidth, $newwidth);
            list($r,$g,$b) = explode(",",$background);
            $color= imagecolorallocate($dest_image, $r, $g, $b);
            $move_space =0;
            if($old_x > $old_y )
            {
                $move_space = ceil(($newwidth - $newheight)/2);
            }
            imagefill($dest_image,0,0,$color);
            imagecopyresampled($dest_image, $source_image, 0, $move_space, 0, 0, $newwidth , $newheight, $old_x, $old_y);
        }

        //Put binary string
        imagejpeg($dest_image,$outFile,$this->options[parent::IMAGE_ITEM_QUALITY]);

        //Destroy images
        imagedestroy($dest_image);
        imagedestroy($source_image);

        //Return boolean
        return  true;
    }

    /**
     * Resize from blob to file
     * @modify nhuantp@fpt.net
     * @param <string> $binary_source
     * @param <int> $newwidth
     * @param <string> $outFile
     * @return <boolean>
     */
    public function resizeKeepWithFromBlobToFile(&$binary_source,$newwidth,$newheight,$outFile)
    {
        //Create image source from string
        $source_image = imagecreatefromstring($binary_source);

        //Check source
        if(!$source_image)
        {
            return false;
        }

        //Get old size
        $old_x = imagesx($source_image);
        $old_y = imagesy($source_image);

        //Check have correct site
        if($old_x <= $newwidth)
        {
            //Destroy image source
            imagedestroy($source_image);

            //Return image file
            return file_put_contents($outFile,$binary_source);
        }

        //Resize keep width
        $newheight = ceil((($newwidth*$old_y)/$old_x));
        $dest_image = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $newwidth , $newheight, $old_x, $old_y);
        
        //Put binary string
        imagejpeg($dest_image,$outFile,$this->options[parent::IMAGE_ITEM_QUALITY]);
        
        //Destroy images
        imagedestroy($dest_image);
        imagedestroy($source_image);

        //Return boolean
        return  true;
    }

    /**
     * Resize from file
     * @param <string> $src_name
     * @param <string> $dest_name
     * @param <int> $newwidth
     * @param <string> $ext
     * @return <boolean>
     */
    public function resizeKeepWithFromFile($src_name,$dest_name,$newwidth,$newheight,$ext)
    {
        //Check extension of file
        if(empty($ext))
        {
            $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
        }
        
        //Create image source from file name
        $source_image = $this->createSourceImage($src_name,$ext);
        
        //Check source
        if(!$source_image)
        {
            return false;
        }

        //Get old size
        $old_x = imagesx($source_image);
        $old_y = imagesy($source_image);
        
        //Check have correct site
        if($old_x <= $newwidth)
        {
            //Destroy image source
            imagedestroy($source_image);

            //Return image file
            return copy($src_name,$dest_name);
        }

        //Resize keep width
        if(empty($newheight))
        {
            $newheight = ceil((($newwidth*$old_y)/$old_x));
        }
        $dest_image = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $newwidth , $newheight, $old_x, $old_y);

        //Put binary string
        imagejpeg($dest_image,$dest_name,$this->options[parent::IMAGE_ITEM_QUALITY]);

        //Destroy images
        imagedestroy($dest_image);
        imagedestroy($source_image);

        //Return boolean
        return  true;
    }

    /**
     * Resize square from file
     * @param <string> $src_name
     * @param <string> $dest_name
     * @param <int> $newwidth
     * @param <string> $background
     * @param <string> $ext
     * @return <boolean>
     */
    public function resizeInSquareFromFile($src_name,$dest_name,$newwidth,$newheight,$background,$ext)
    {
        //Check extension of file
        if(empty($ext))
        {
            $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
        }

        //Create image source from file name
        $source_image = $this->createSourceImage($src_name,$ext);
        
        //Check source
        if(!$source_image)
        {
            return false;
        }

        //Check background
        if(empty($background))
        {
            $background = $this->options[self::IMAGE_ITEM_BACKGROUND];
        }

        //Check new height
        if(empty($newheight))
        {
            $newheight = $newwidth;
        }

        //Get old size
        $old_x = imagesx($source_image);
        $old_y = imagesy($source_image);

        //Check have correct site
        if($old_x <= $newwidth && $old_y <= $newheight)
        {
            //Destroy image source
            imagedestroy($source_image);

            //Return image file
            return copy($src_name,$dest_name);
        }

        //Resize keep width
        if($old_x < $newwidth)
        {
            //Check size
            $newwidth = $old_x;
            if($newwidth > $newheight)
            {
                $newwidth = $newheight;
            }

            //Crop image            
            $dest_image = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $newwidth , $newheight, $newwidth, $newheight);
        }
        else
        {
            $newheight = ceil((($newwidth*$old_y)/$old_x));
            $dest_image = imagecreatetruecolor($newwidth, $newwidth);
            list($r,$g,$b) = explode(",",$background);
            $color= imagecolorallocate($dest_image, $r, $g, $b);
            $move_space =0;
            if($old_x > $old_y )
            {
                $move_space = ceil(($newwidth - $newheight)/2);
            }

            //Check size
            if($old_y < $newwidth)
            {
                $newwidth = $old_y;
                $newheight = $newwidth;
            }            

            //Crop image
            imagefill($dest_image,0,0,$color);
            imagecopyresampled($dest_image, $source_image, 0, -$move_space, 0, 0, $newwidth , $newheight, $old_x, $old_y);
        }

        //Put binary string
        imagejpeg($dest_image,$dest_name,$this->options[parent::IMAGE_ITEM_QUALITY]);

        //Destroy images
        imagedestroy($dest_image);
        imagedestroy($source_image);

        //Return boolean
        return  true;
    }
}