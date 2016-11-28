<?php
/**
 * Autogenerated by Thrift Compiler (0.8.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';


$GLOBALS['gnt_mobion_storage_mbdefinition_E_ResponseCode'] = array(
  'SUCESSFUL' => 0,
  'ACCESS_DENIED' => 1,
  'WRONG_PARAM' => 2,
  'NOT_ENOUGH_COIN' => 3,
  'ACCOUNT_INACTIVE' => 4,
  'ACCOUNT_NOT_FOUND' => 5,
  'TRANS_NOT_FOUND' => 6,
  'CSV_INITIALIZED' => 7,
  'CSV_NOT_FOUND' => 8,
  'INTERNAR_ERROR' => 9,
);

final class ResponseCode {
  const SUCESSFUL = 0;
  const ACCESS_DENIED = 1;
  const WRONG_PARAM = 2;
  const NOT_ENOUGH_COIN = 3;
  const ACCOUNT_INACTIVE = 4;
  const ACCOUNT_NOT_FOUND = 5;
  const TRANS_NOT_FOUND = 6;
  const CSV_INITIALIZED = 7;
  const CSV_NOT_FOUND = 8;
  const INTERNAR_ERROR = 9;
  static public $__names = array(
    0 => 'SUCESSFUL',
    1 => 'ACCESS_DENIED',
    2 => 'WRONG_PARAM',
    3 => 'NOT_ENOUGH_COIN',
    4 => 'ACCOUNT_INACTIVE',
    5 => 'ACCOUNT_NOT_FOUND',
    6 => 'TRANS_NOT_FOUND',
    7 => 'CSV_INITIALIZED',
    8 => 'CSV_NOT_FOUND',
    9 => 'INTERNAR_ERROR',
  );
}

class gnt_mobion_storage_mbdefinition_GeneralResponse {
  static $_TSPEC;

  public $statusCode = -1;
  public $msg = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'statusCode',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'msg',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['statusCode'])) {
        $this->statusCode = $vals['statusCode'];
      }
      if (isset($vals['msg'])) {
        $this->msg = $vals['msg'];
      }
    }
  }

  public function getName() {
    return 'GeneralResponse';
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
            $xfer += $input->readI32($this->statusCode);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->msg);
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
    $xfer += $output->writeStructBegin('GeneralResponse');
    if ($this->statusCode !== null) {
      $xfer += $output->writeFieldBegin('statusCode', TType::I32, 1);
      $xfer += $output->writeI32($this->statusCode);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->msg !== null) {
      $xfer += $output->writeFieldBegin('msg', TType::STRING, 2);
      $xfer += $output->writeString($this->msg);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

?>
