<?php

// класс предоставляет методы для проверки и выдачи доступа

include_once __DIR__.'/Admin.php';

class Access extends Admin {

    function __construct($instance, $session_name, $hash_hardness = 16) {
        $this->instance = $instance;
        $this->session_name = $session_name;
        $this->hash_hardness = $hash_hardness;
    }

    function generateSessionHash() {
        return $this->strgen('0123456789abcdef', $this->$hash_hardness);
    }

    function checkAccess($hash) {

    }

    function grantAccessToUserName($username) {

    }

    function grantAccessToUserId($uuid) {

    }

    function removeAccessFrom($hash) {

    }

    function removeAccessFromUserName($username) {

    }

    function removeAccessFromUserId($uuid) {

    }

}

?>