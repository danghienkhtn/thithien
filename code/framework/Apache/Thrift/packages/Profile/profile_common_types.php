<?php
/**
 * Autogenerated by Thrift Compiler (0.8.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';


$GLOBALS['gnt_mobion_storage_profile_E_EnumBirthdatePrivacy'] = array(
  'ShowNone' => 1,
  'ShowDate' => 2,
  'ShowYear' => 3,
  'ShowFull' => 4,
);

final class EnumBirthdatePrivacy {
  const ShowNone = 1;
  const ShowDate = 2;
  const ShowYear = 3;
  const ShowFull = 4;
  static public $__names = array(
    1 => 'ShowNone',
    2 => 'ShowDate',
    3 => 'ShowYear',
    4 => 'ShowFull',
  );
}

$GLOBALS['gnt_mobion_storage_profile_E_EnumRelationship'] = array(
  'None' => 0,
  'Single' => 1,
  'Married' => 2,
);

final class EnumRelationship {
  const None = 0;
  const Single = 1;
  const Married = 2;
  static public $__names = array(
    0 => 'None',
    1 => 'Single',
    2 => 'Married',
  );
}

$GLOBALS['gnt_mobion_storage_profile_E_EnumGender'] = array(
  'None' => 0,
  'Male' => 1,
  'Female' => 2,
  'Other' => 3,
);

final class EnumGender {
  const None = 0;
  const Male = 1;
  const Female = 2;
  const Other = 3;
  static public $__names = array(
    0 => 'None',
    1 => 'Male',
    2 => 'Female',
    3 => 'Other',
  );
}

$GLOBALS['gnt_mobion_storage_profile_E_EnumSocialNetwork'] = array(
  'fb' => 0,
  'gg' => 1,
  'tw' => 2,
  'mx' => 3,
);

final class EnumSocialNetwork {
  const fb = 0;
  const gg = 1;
  const tw = 2;
  const mx = 3;
  static public $__names = array(
    0 => 'fb',
    1 => 'gg',
    2 => 'tw',
    3 => 'mx',
  );
}

$GLOBALS['gnt_mobion_storage_profile_E_EnumInterests'] = array(
  'Music' => 1,
  'Reading' => 2,
  'Games' => 4,
  'Movies' => 8,
  'TV_Shows' => 16,
  'Sports' => 32,
  'Last' => 2147483647,
);

final class EnumInterests {
  const Music = 1;
  const Reading = 2;
  const Games = 4;
  const Movies = 8;
  const TV_Shows = 16;
  const Sports = 32;
  const Last = 2147483647;
  static public $__names = array(
    1 => 'Music',
    2 => 'Reading',
    4 => 'Games',
    8 => 'Movies',
    16 => 'TV_Shows',
    32 => 'Sports',
    2147483647 => 'Last',
  );
}

class gnt_mobion_storage_profile_TokenDetail {
  static $_TSPEC;

  public $token_type = null;
  public $create_time = null;
  public $expired_in = null;
  public $appid = null;
  public $deviceid = null;
  public $app_version = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'token_type',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'create_time',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'expired_in',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'appid',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'deviceid',
          'type' => TType::STRING,
          ),
        6 => array(
          'var' => 'app_version',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['token_type'])) {
        $this->token_type = $vals['token_type'];
      }
      if (isset($vals['create_time'])) {
        $this->create_time = $vals['create_time'];
      }
      if (isset($vals['expired_in'])) {
        $this->expired_in = $vals['expired_in'];
      }
      if (isset($vals['appid'])) {
        $this->appid = $vals['appid'];
      }
      if (isset($vals['deviceid'])) {
        $this->deviceid = $vals['deviceid'];
      }
      if (isset($vals['app_version'])) {
        $this->app_version = $vals['app_version'];
      }
    }
  }

  public function getName() {
    return 'TokenDetail';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->token_type);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->create_time);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->expired_in);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->appid);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->deviceid);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->app_version);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('TokenDetail');
    if ($this->token_type !== null) {
      $xfer += $output->writeFieldBegin('token_type', TType::I32, 1);
      $xfer += $output->writeI32($this->token_type);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->create_time !== null) {
      $xfer += $output->writeFieldBegin('create_time', TType::I32, 2);
      $xfer += $output->writeI32($this->create_time);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->expired_in !== null) {
      $xfer += $output->writeFieldBegin('expired_in', TType::I32, 3);
      $xfer += $output->writeI32($this->expired_in);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->appid !== null) {
      $xfer += $output->writeFieldBegin('appid', TType::STRING, 4);
      $xfer += $output->writeString($this->appid);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->deviceid !== null) {
      $xfer += $output->writeFieldBegin('deviceid', TType::STRING, 5);
      $xfer += $output->writeString($this->deviceid);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->app_version !== null) {
      $xfer += $output->writeFieldBegin('app_version', TType::STRING, 6);
      $xfer += $output->writeString($this->app_version);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class gnt_mobion_storage_profile_RecentAvatar {
  static $_TSPEC;

  public $avatar_url = null;
  public $created_at = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'avatar_url',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'created_at',
          'type' => TType::I64,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['avatar_url'])) {
        $this->avatar_url = $vals['avatar_url'];
      }
      if (isset($vals['created_at'])) {
        $this->created_at = $vals['created_at'];
      }
    }
  }

  public function getName() {
    return 'RecentAvatar';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->avatar_url);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->created_at);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('RecentAvatar');
    if ($this->avatar_url !== null) {
      $xfer += $output->writeFieldBegin('avatar_url', TType::STRING, 1);
      $xfer += $output->writeString($this->avatar_url);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->created_at !== null) {
      $xfer += $output->writeFieldBegin('created_at', TType::I64, 2);
      $xfer += $output->writeI64($this->created_at);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

?>
