<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Image_Adapter_Imagemagick
 * @version     :   201008
 * @copyright   :   My company
 * @todo        :   Using to images
 */
class Core_Image_Adapter_Imagemagick extends Core_Image_Adapter_Abstract
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
     * Resize but keep from blob
     * @param <string> $binary_source
     * @param <int> $newwidth
     * @param <string> $ext
     * @return <string>
     */
    public function resizeKeepWithFromBlob(&$binary_source,$newwidth,$ext)
    {
        //Init magick
        $img = new Imagick();

        //Reads image from a binary string
        $img->readImageBlob($binary_source);

        //Check width of images
        if($img->getImageWidth() > $newwidth)
        {
            $img->thumbnailImage($newwidth,null);
        }

        //Check extension of file
        if(empty($ext))
        {
            $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
        }
        
        //Set format type of image
        $img->setImageFormat($ext);
        
        //Set quality of image
        $img->setImageCompressionQuality($this->options[parent::IMAGE_ITEM_QUALITY]);

        //Return blob image data
        return (string)$img;
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
        //Init magick
        $img = new Imagick();

        //Reads image from a binary string
        $img->readImageBlob($binary_source);

        //Check background
        if(empty($background))
        {
            $background = $this->options[self::IMAGE_ITEM_BACKGROUND];
        }

        //Check width of images
        if($img->getImageWidth() > $newwidth || $img->getImageHeight() > $newwidth )
        {
            if($img->getImageWidth() < $newwidth)
            {
                $img->cropImage($img->getImageWidth(),$newwidth,0,0);
            }
            else
            {
                $img->thumbnailImage($newwidth,null);
                if($img->getImageHeight() > $newwidth)
                {
                    $img->cropImage($newwidth,$newwidth,0,0);
                }
                elseif($img->getImageHeight() < $newwidth)
                {
                    $move_space  = ceil(($newwidth - $img->getImageHeight())/2);
                    $newimg = new Imagick();
                    $newimg->newImage($newwidth, $newwidth, $this->parseBackground($background));
                    $newimg->setImageFormat($img->getImageFormat());
                    $newimg->compositeImage($img, imagick::COMPOSITE_OVER, 0, $move_space);
                    $img =  $newimg;
                }
            }
        }

        //Check extension of file
        if(empty($ext))
        {
            $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
        }

        //Set format type of image
        $img->setImageFormat($ext);
        
        //Set quality of image
        $img->setImageCompressionQuality($this->options[parent::IMAGE_ITEM_QUALITY]);

        //Return blob image data
        return (string)$img;
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
        //Init magick
        $img = new Imagick();

        //Reads image from a binary string
        $img->readImageBlob($binary_source);
		
        if ( !$newwidth ) {
        	$newwidth	= $img->getImageWidth();
        }//end if
        
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

        //Check width of images
        if($img->getImageWidth() > $newwidth || $img->getImageHeight() > $newheight )        
        {
            if($img->getImageWidth() < $newwidth)
            {
                //Check size
                $newwidth = $img->getImageWidth();
                if($newwidth > $newheight)
                {
                    $newwidth = $newheight;
                }

                //Crop image
                $img->cropImage($newwidth,$newwidth,0,0);
            }
            else
            {
                //Check size to thumb
                if($img->getImageWidth() > $img->getImageHeight())
                {
                     //Thumb images
                    $img->thumbnailImage(0, $newheight);
                }                
                else
                {
                    //Thumb images
                    $img->thumbnailImage($newwidth, 0);
                }
                
                //Check to crop
                if($img->getImageHeight() > $img->getImageWidth())
                {
                    $img->cropImage($newwidth,$newwidth,0,0);
                }
                elseif($img->getImageHeight() < $img->getImageWidth())
                {
                    //Check size
                    $move_space  = ceil(($img->getImageWidth() - $img->getImageHeight())/2);

                    //Crop image
                    $newimg = new Imagick();
                    $newimg->newImage($newheight, $newheight, $this->parseBackground($background));
                    $newimg->setImageFormat($img->getImageFormat());
                    $newimg->compositeImage($img, imagick::COMPOSITE_OVER, -$move_space, 0);
                    $img =  $newimg;
                }
            }
        }
        
        //Check extension of file
        if(empty($ext))
        {
            $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
        }

        //Set format type of image
        $img->setImageFormat($ext);

        //Set quality of image
        $img->setImageCompressionQuality($this->options[parent::IMAGE_ITEM_QUALITY]);

        //Write binary string to file
        $img->writeImage($outFile);

        //Return true if okie
        return true;
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
        //Init magick
        $img = new Imagick();

        //Reads image from a binary string
        $img->readImageBlob($binary_source);
		
        //set width
        if ( !$newwidth ) {
        	$newwidth	= $img->getImageWidth();
        }//end if
        
        //Check width of images
        if($img->getImageWidth() > $newwidth)
        {
            $img->thumbnailImage($newwidth,$newheight);
        }        

        //Check extension of file
        if(empty($ext))
        {
            $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
        }

        //Set format type of image
        $img->setImageFormat($ext);

        //Set quality of image
        $img->setImageCompressionQuality($this->options[parent::IMAGE_ITEM_QUALITY]);

        //Write binary string to file
        $img->writeImage($outFile);

        //Return true if okie
        return true;
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
        //Init magick
        $img = new Imagick();

        //Reads image from source file path
        $img->readImage($src_name);

        //Check width of images
        if($img->getImageWidth() > $newwidth)
        {
            $img->thumbnailImage($newwidth,$newheight);
        }        

        //Check extension of file
        if(empty($ext))
        {
            $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
        }

        //Set format type of image
        $img->setImageFormat($ext);

        //Set quality of image
        $img->setImageCompressionQuality($this->options[parent::IMAGE_ITEM_QUALITY]);

        //Write to file
        $img->writeImage($dest_name);

        //Return true if okie
        return true;
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
        //Init magick
        $img = new Imagick();

        //Reads image from source file path
        $img->readImage($src_name);

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
       
        //Check width of images
        if($img->getImageWidth() > $newwidth || $img->getImageHeight() > $newheight )
        {
            if($img->getImageWidth() < $newwidth)
            {
                //Check size
                $newwidth = $img->getImageWidth();
                if($newwidth > $newheight)
                {
                    $newwidth = $newheight;
                }

                //Crop image
                $img->cropImage($newwidth,$newwidth,0,0);
            }
            else
            {
                //Check size to thumb
                if($img->getImageWidth() > $img->getImageHeight())
                {
                     //Thumb images
                    $img->thumbnailImage(0, $newheight);
                }                
                else
                {
                    //Thumb images
                    $img->thumbnailImage($newwidth, 0);
                }
               
                //Check to crop
                if($img->getImageHeight() > $img->getImageWidth())
                {                 
                    $img->cropImage($newwidth,$newwidth,0,0);
                }
                elseif($img->getImageHeight() < $img->getImageWidth())
                {                  
                    //Check size
                    $move_space  = ceil(($img->getImageWidth() - $img->getImageHeight())/2);
                   
                    //Crop image                    
                    $newimg = new Imagick();
                    $newimg->newImage($newheight, $newheight, $this->parseBackground($background));
                    $newimg->setImageFormat($img->getImageFormat());                    
                    $newimg->compositeImage($img, imagick::COMPOSITE_OVER, -$move_space, 0);
                    $img =  $newimg;
                }
            }

            //Check extension of file
            if(empty($ext))
            {
                $ext = $this->options[parent::IMAGE_ITEM_DEFAULT_TYPE];
            }

            //Set format type of image
            $img->setImageFormat($ext);

            //Set quality of image
            $img->setImageCompressionQuality($this->options[parent::IMAGE_ITEM_QUALITY]);
        
            //Write to file
            $img->writeImage($dest_name);
        }
        else
        {
            //Copy source file to destination file
            copy($src_name,$dest_name);
        }

        //Return true if okie
        return true;
    }
}