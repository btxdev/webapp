<?php

// класс предоставляет методы для проверки и выдачи доступа

include_once __DIR__.'/Admin.php';

class Status {
    function __construct($status, $session='', $msg='') {

        $this->status = $status;
        $this->session = $session;
        $this->msg = $msg;

        switch($status) {
            case 'OK':
                break;
            case 'USER_NOT_FOUND':
            case 'WRONG_PASSWORD':
                $this->status = 'WRONG_PASSWORD';
                $this->msg = 'Неверный логин или пароль, проверьте введенные данные.';
        }
        
    }
}

class Access extends Admin {

    function __construct($instance, $session_name, $hash_hardness = 16) {
        $this->instance = $instance;
        $this->session_name = $session_name;
        $this->hash_hardness = $hash_hardness;
        $this->uuid = -1;
        $this->username = -1;
    }

    function generateSessionHash() {
        return $this->strgen('0123456789abcdef', $this->hash_hardness);
    }

    function getUserIdBySessionHash($hash) {
        $result = $this->fetch(
            'SELECT `employee_id` 
            FROM `sessions` 
            WHERE `sesshash` LIKE :hash',
            [
                ':hash' => $hash
            ]
        );
        if($result != false) $this->uuid = $result;
        return $result;
    }

    function isAccessGranted($hash) {
        $result = $this->getUserIdBySessionHash($hash);
        return ($result != false);
    }

    function grantAccessToUserId($uuid) {
        // проверка существования пользователя
        if ($uuid == false) return false;
        // создание записи в БД
        $hash = $this->generateSessionHash();
        $this->run(
            'INSERT INTO sessions 
            (sesshash, employee_id, created)
            VALUES (:sesshash, :employee_id, :created)',
            [
                ':sesshash' => $hash,
                ':employee_id' => $uuid,
                ':created' => date('Y-m-d')
            ]
        );
        return $hash;
    }

    function grantAccessToUserName($username) {
        $uuid = $this->getUserId($username);
        return $this->grantAccessToUserId($uuid);
    }

    function removeAccessFrom($hash) {
        $this->run(
            'DELETE FROM `sessions` WHERE `sesshash` LIKE :sesshash',
            [
                ':sesshash' => $hash
            ]
        );
    }

    function removeAccessFromUserId($uuid) {
        $this->run(
            'DELETE FROM `sessions` WHERE `employee_id` LIKE :uuid',
            [
                ':uuid' => $uuid
            ]
        );
    }

    function removeAccessFromUserName($username) {
        $uuid = $this->getUserId($username);
        $this->removeAccessFromUserId($uuid);
    }

    function login($username, $password_raw) {

        $uuid = $this->getUserId($username);
        if($uuid == false) 
            return new Status('USER_NOT_FOUND');

        $data = $this->fetch(
            'SELECT password, salt FROM employees WHERE employee_id LIKE :uuid',
            [
                ':uuid' => $uuid
            ]
        );
        if($data == false) 
            return new Status('USER_NOT_FOUND');

        $hash = $data['password'];
        $salt = $data['salt'];

        if(!$this->verifyPassword($password_raw, $salt, $hash))
            return new Status('WRONG_PASSWORD');

        return new Status('OK', $this->grantAccessToUserId($uuid));

    }

}

?>