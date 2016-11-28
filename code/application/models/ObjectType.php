<?php

/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 7/14/2016
 * Time: 1:57 PM
 */
class ObjectType
{
    public static $feed                 = 1;
    public static $feedComment          = 11;
    public static $feedLike             = 111;
    public static $feedShare            = 1111;
    public static $feedUserTagPost      = 11111;
    public static $feedUserMentionedComment   = 111111;
    public static $photo = 2;
    public static $photoComment = 22;
    public static $photoLike = 222;

    public static $notificationActivities = 3;

    public static $document         = 4;
    public static $documentNew      = 44;
    public static $documentShare    = 444;
    public static $documentDelete   = 4444;
    public static $documentUpload   = 44444;
    public static $documentRename   = 444444;

    public static $group            = 5;
    public static $groupInvite      = 55;
    public static $groupRequest     = 555;
    public static $groupAccept      = 5555;
    public static $groupLeave       = 55555;
    public static $groupCancel      = 555555;
    public static $groupRemove      = 5555555;
    public static $groupNotNow      = 55555555;
    public static $groupSuggestion  = 555555555;

    public static function parserToName($iType)
    {
        $sName = '';
        switch($iType){

            case ObjectType::$feed :{
                $sName = 'feed';
                break;
            }

            case ObjectType::$photo :{
                $sName = 'photo_feed';
                break;
            }

            case ObjectType::$group :{
                $sName = 'group';
                break;
            }
        }

        return $sName;
    }

}