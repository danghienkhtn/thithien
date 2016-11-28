<?php

/**
 * @author      :   HoaiTN
 * @name        :   Model_API
 * @version     :   201011
 * @copyright   :   My company
 * @todo        :   Api model
 */
class ApiPhoto {

   
    private static $sToken = 'FDFRGDdfdhfsjfhsj';

    public static function deletePhotos($photoIds, $token)
    {
        $arrLogin = Admin::getInstance()->getLogin();
        if($token != Core_Cookie::getCookie(TOKEN_API) || (!isset($arrLogin['accountID']) || empty($arrLogin['accountID'])))
            return Core_Server::setOutputData(true, 'token is expire', array());

        $message = 'Photo not found';
        $error = true;
        $photoIds = urldecode($photoIds);
        $arr_photoId = json_decode($photoIds,true);// string json
//        var_dump($arr_photoId);die;
        if(is_array($arr_photoId) && !empty($arr_photoId)) {

            $arrLogin = Admin::getInstance()->getLogin();
            $arr_photoId = array_filter($arr_photoId);
            $arr_photo = PhotoFeed::getInstance()->getPhotoFeedByIds($arr_photoId);

            foreach ($arr_photo['data'] as $photo) {
                if (!empty($photo)) {

                    $iGroupId = $photo['group_id'];
                    $iAlbumId = $photo['album_id'];
                    $album = Album::getInstance()->getAlbumByID($iAlbumId);
                    if (!Core_Common::checkOwnerOfObject($photo, $arrLogin['accountID'])&& !Core_Common::checkOwnerOfObject($album, $arrLogin['accountID']))
                        return Core_Server::setOutputData(true, 'access is denied', 'access is denied');


                    // create log
                    ActionLog::getInstance()->insert($arrLogin['id'], ActionLog::$delete, ActionLog::$photo, $arrLogin['accountID'], $arrLogin['nickName'], ' a photo Of "' . $album['name'] . '" album');
                    // process photo and name
                    $photo = Core_Common::photoProcess($photo);

                    $fileName = trim($photo['image_url']);
                    // delete file
                    if (!empty($fileName))
                        Core_Image::delete($fileName);

                    //delete photo_feed
                    $feedId = $photo['feed_id'];
                    if (!empty($feedId)) {

                         PhotoFeed::getInstance()->removePhoto($photo['photo_id'], $photo['group_id'], $photo['album_id'], $feedId);
                        $total = PhotoFeed::getInstance()->countPhotoFeedIDByFeedId($feedId);

                        if ($total == 0) {
                            // create log
                            ActionLog::getInstance()->insert($arrLogin['id'], ActionLog::$delete, ActionLog::$feed, $arrLogin['accountID'], $arrLogin['nickName'], ' a feed ' . $feedId);
                            //delete feed
                            Feed::getInstance()->delete($feedId, $iGroupId);
                        }
                    }

                    //delete album
                    $total = PhotoFeed::getInstance()->countPhotoFeedIDByGroupIdAndAlbumId($iGroupId, $iAlbumId);

                    if ($total == 0) {
                        // create log
                        ActionLog::getInstance()->insert($arrLogin['id'], ActionLog::$delete, ActionLog::$album, $arrLogin['accountID'], $arrLogin['nickName'], ' "' . $album['name'] . '" album');

                        // process album and get path file album
                        $album = Core_Common::albumProcess($album);
                        $fileName = $album['image_tag'];
                        // delete file album
                        if (!empty($fileName))
                            Core_Image::delete($fileName);

                        // delete a album
                        Album::getInstance()->removeAlbum($iAlbumId);
                    }

                    Album::getInstance()->updateAlbumTotal($iAlbumId, $total);
                    $message = 'Success';
                    $error = false;
                }else {
                    return Core_Server::setOutputData(true, 'photo not found');
                }
            }
        }
        return Core_Server::setOutputData($error, $message, array());
    }

    public function downloadPhoto($url,$apiKey = ''){

        $arrLogin = Admin::getInstance()->getLogin($apiKey);
        if(empty($arrLogin)){
            return Core_Server::setOutputData(true, 'You must be login. Please!!!', array());
        }

        $uploadUrl = explode('upload/images',$url);
        if(count($uploadUrl) < 2){
            return Core_Server::setOutputData(true, 'Sorry. Not support link', array());
        }
        $sUploadUrl = PATH_IMAGES_UPLOAD_DIR.$uploadUrl[1];

        if(!file_exists($sUploadUrl)){
            return Core_Server::setOutputData(true, "Sorry, the page you requested doesn't exist.", array());
        }

        $url = explode('/',$url);
        $originalName = end($url);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$originalName.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($sUploadUrl));
        readfile($sUploadUrl);

        return Core_Server::setOutputData(false, "Success.", array());
    }
}
