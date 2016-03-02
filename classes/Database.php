<?php

class Database extends PDO {

    static protected $db;

    public function __construct() {
        $dbhost = DatabaseSettings::$db_host;
        $dbname = DatabaseSettings::$db_name;
        $user = DatabaseSettings::$db_user;
        $pass = DatabaseSettings::$db_password;
        parent::__construct("mysql:host=$dbhost;dbname=$dbname", $user, $pass);
    }
    
    public static function &getDB() {
        if (!isset(self::$db))
            self::$db = new Database();
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$db;
    }

}

