<?php

/**
 * Created by PhpStorm.
 * User: thanh.lh
 * Date: 6/14/2016
 * Time: 11:13 AM
 */
class Fields
{
    private static $fieldSys = array('db','instance','name','collection','collection_name');
    public function removeFields($table,$arrData){

        $reflect = new ReflectionClass($table);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);
        $newTable = array();

        foreach($props as $prop){
//            $name = substr($prop->name,1);
            $reflectionProperty = $reflect->getProperty($prop->name);
            $reflectionProperty->setAccessible(true);

            $val = $reflectionProperty->getValue(new $table);

            if(in_array($val,self::$fieldSys))
                continue;

            if(!is_string($val))
                continue;


            if(isset($arrData[$val]))
                $newTable[$val] = $arrData[$val];


        }

        return $newTable;
    }

    public function getDefaultFields($instance,$_this,$tableName)
    {
        $reflect = new ReflectionClass($instance);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);
        $fields = array();

        $sClassName =  get_class($_this);

        foreach($props as $prop){
            $name = substr($prop->name,1);
            $class = $prop->class;

            if(in_array($name,self::$fieldSys) || $class != $sClassName)
                continue;

            $fields []= $tableName.".$name";

        }
        $sFields = implode(',',$fields);
        return $sFields;
    }
}