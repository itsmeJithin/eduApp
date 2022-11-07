<?php


namespace App\Utils;

/**
 *
 * @Date 07/06/21
 */
class CommonUtil
{
    public static function convertObjectToUTF8Format($object)
    {
        if (is_array($object)) {
            foreach ($object as $key => $value) {
                $object[$key] = CommonUtil::convertObjectToUTF8Format($value);
            }
        } elseif (is_string($object)) {
            return mb_convert_encoding($object, "UTF-8", "UTF-8");
        } elseif (is_object($object)) {
            foreach ($object as $key => $value) {
                $object->{$key} = CommonUtil::convertObjectToUTF8Format($value);
            }
        }
        return $object;
    }

}
