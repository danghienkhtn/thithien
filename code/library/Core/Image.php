<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Core_Image
{
  
    
	public static function resizeImage($org_file, $des_file, $default_width, $default_height ){
     	 
     	
     	$arrResult = array("error" => 1, 'message' => "error");
     	
//     	$imgtype;
//     	$magick;
//     	$rate;
//     	$rate_width;
//     	$rate_height;
//     	$org_width;
//     	$org_height;
     	
     	try{
     		 
     		 
     		list($width, $height) = getimagesize($org_file);

     		// check if the file is really an image
     		if ($width == null && $height == null) {
     			
     			$arrResult = array("error" => 1, 'message' => "invalid image");
     			return $arrResult;
     		}
     		
     		$magick = new Imagick($org_file);

     		//check max lenght
     		if($magick->getImageLength() > MAX_UPLOAD_SIZE){
     			
     			$arrResult = array("error" => 1, 'message' => "Please upload no more than 3MB.");
     			return $arrResult;
     			
     		}

     		$d = $magick->getImageGeometry();

     		$org_width = $d['width'];
     		$org_height = $d['height'];

     		$org_width == 0 ? $rate_width = 0 : $rate_width = $default_width / $org_width;
     		$org_height == 0 ? $rate_height = 0 : $rate_height = $default_height / $org_height;
     		if($default_height > $org_height && $default_width > $org_width){
     			$magick->resizeImage($org_width, $org_height, Imagick::FILTER_LANCZOS, 0.8);
     		}else{
     			if($rate_width != 0 && $rate_height != 0){
     				if ($rate_width < $rate_height){
     					$rate = $rate_width;  #fix width
     					$magick->resizeImage( $default_width,  $org_height * $rate, Imagick::FILTER_LANCZOS, 0.8);
     				}else{
     					$rate = $rate_height;  #fix height
     					$magick->resizeImage( $org_width * $rate,  $default_height, Imagick::FILTER_LANCZOS, 0.8);
     
     				}
     			}else{
     				$rate = $rate_width;  #fix width
     				$magick->resizeImage(  $default_width,  $org_height * $rate, Imagick::FILTER_LANCZOS, 0.8);
     			}
     		}

			$orientation = $magick->getImageOrientation();
			switch($orientation) {
				case imagick::ORIENTATION_BOTTOMRIGHT:
					$magick->rotateimage("#000", 180); // rotate 180 degrees
					break;

				case imagick::ORIENTATION_RIGHTTOP:
					$magick->rotateimage("#000", 90); // rotate 90 degrees CW
					break;

				case imagick::ORIENTATION_LEFTBOTTOM:
					$magick->rotateimage("#000", -90); // rotate 90 degrees CCW
					break;
			}

			// Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
			$magick->setImageOrientation(imagick::ORIENTATION_TOPLEFT);

     		$magick->writeImage($des_file);
     		 
     		$magick->clear();
     		$magick->destroy();
     		chmod($des_file, 0664);
     		
     		$arrResult = array("error" => 0, 'message' => "");
     	}
     	catch (Exception $ex){
     		$arrResult = array("error" => 1, 'message' => $ex->getMessage());
     	}
     	return $arrResult;
     }
     
     public static function cropImage($org_file, $des_file, $default_width, $default_height ){
     	 
     
     	$arrResult = array("error" => 1, 'message' => "error");
     
//     	$imgtype;
//     	$magick;
//     	$rate;
//     	$rate_width;
//     	$rate_height;
//     	$org_width;
//     	$org_height;
     
     	try{
     
     
     		list($width, $height) = getimagesize($org_file);
     		 
     		// check if the file is really an image
     		if ($width == null && $height == null) {
     
     			$arrResult = array("error" => 1, 'message' => "invalid image");
     			return $arrResult;
     		}

     		 
     		$magick = new Imagick($org_file);
     		//check max lenght
     		if($magick->getImageLength() > MAX_UPLOAD_SIZE){
     
     			$arrResult = array("error" => 1, 'message' => "Please upload no more than 3MB.");
     			return $arrResult;
     
     		}
     		
     		if(!($default_width >= $width && $default_height >= $height)){
     			if($default_width > $width){
     				$default_width = $width;
     			}
     			 
     			if($default_height > $height){
     				$default_height = $height;
     			}

                $orientation = $magick->getImageOrientation();

                switch($orientation) {
                    case imagick::ORIENTATION_BOTTOMRIGHT:
                        $magick->rotateimage("#000", 180); // rotate 180 degrees
                        break;

                    case imagick::ORIENTATION_RIGHTTOP:
                        $magick->rotateimage("#000", 90); // rotate 90 degrees CW
                        break;

                    case imagick::ORIENTATION_LEFTBOTTOM:
                        $magick->rotateimage("#000", -90); // rotate 90 degrees CCW
                        break;
                }

                // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
                $magick->setImageOrientation(imagick::ORIENTATION_TOPLEFT);

     			$magick->cropThumbnailImage( $default_width, $default_height );
     		}
     		
     		
     		
     		$magick->writeImage($des_file);
     
     		$magick->clear();
     		$magick->destroy();
     		chmod($des_file, 0664);
     		 
     		$arrResult = array("error" => 0, 'message' => "");
     	}
     	catch (Exception $ex){
     		$arrResult = array("error" => 1, 'message' => $ex->getMessage());
     	}
     	return $arrResult;
     }
     
     public static function delete($image_url){
     	if(file_exists($image_url)){
     		unlink($image_url);
     	}
     }

    public static function autoRotateImage($image) {
        $orientation = $image->getImageOrientation();

        switch($orientation) {
            case imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage("#000", 180); // rotate 180 degrees
                break;

            case imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage("#000", 90); // rotate 90 degrees CW
                break;

            case imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                break;
        }

        // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets saved with the image!
        $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
    }
}
