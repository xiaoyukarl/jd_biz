<?php
/**
 * Created by PhpStorm.
 * User: xiaoyukarl
 * Date: 2020-01-13
 * Time: 12:04
 */

namespace jd\biz\lib;


/**
 * 获取config
 * Class ConfigLib
 * @package jd\biz\lib
 */
class ConfigLib
{
    protected static  $configs = [];

    public static function getConfigs()
    {
        if(empty(self::$configs)){
            $file = __DIR__ . '/../configs/config.php';
            self::$configs = include_once $file;
        }
        return self::$configs;
    }

    public static function get($key = '')
    {
        self::getConfigs();
        return isset(self::$configs[$key]) ? self::$configs[$key] : '';
    }

    public function set($key, $val)
    {
        self::getConfigs();
        self::$configs[$key] = $val;
    }

}