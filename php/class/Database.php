<?php

// класс-обертка поверх PDO, если потребуется его замена в будущем
// класс описывает базовый функционал для работы с БД

class Database extends PDO {

    public $instance;

    function __construct($host, $db, $user, $password, $charset = 'utf8mb4') {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->instance = new PDO($dsn, $user, $password, $options);
    }

    function run($sql, $args = null) {
        $stmt = $this->instance->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }

    function fetch($sql, $args = null) {
        $stmt = $this->run($sql, $args);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }




// fetch column

// fetch all

// count

// insert line in table

// remove line

// update line

// create table

// drop table



}

?>