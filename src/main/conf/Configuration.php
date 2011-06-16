<?php

class Configuration
{
    private static $connection;

    /**
     * @static
     * @return array
     */
    public static function getConfig()
    {
        if (strpos($_SERVER['SERVER_NAME'], 'localhost') > 0) {
            return array('host'=>'localhost',
                         'user'=>'root',
                         'pass'=>'root',
                         'name'=>'tweakers_case');
        }
        else {
            return array('host'=>'10.194.111.8',
                         'user'=>'user_0b8e3503',
                         'pass'=>'&Rw02SY4I^X5JX',
                         'name'=>'db_0b8e3503');
        }
    }

    /**
     * @static
     * @return PDO
     */
    public static function getConnection()
    {
        if (self::$connection == null) {
            $config = self::getConfig();

            self::$connection = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['name'],
            $config['user'],
            $config['pass']);
        }

        return self::$connection;
    }
}
