<?php

// класс расширяет функционал класса Database для работы с БД
// класс предоставляет методы для управления сайтом

include_once __DIR__.'/Database.php';

class Admin extends Database {

    function __construct($instance) {
        $this->instance = $instance;
    }

    function getUserId($username) {
        $rows = $this->fetch(
            'SELECT `uuid` FROM users WHERE uname=?',
            [$username]
        );
        if($rows == false) return false;
        else {
            return intval($rows['uuid']);
        }
    }

    function createUser($username, $password) {
        // игнор, если пользователь уже существует
        if($this->getUserId($username) != false) {
            return false;
        }
        // создание записи в БД
        $this->run(
            'INSERT INTO users 
            (uname, password)
            VALUES (:uname, :password)',
            [
                ':uname' => $username,
                ':password' => $this->hashPassword($password)
            ]
        );
        // id созданного пользователя
        $id = $this->getUserId($username);
        return $id;
    }

    function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    function removeUser($uuid) {
        // удаление записи
        $this->run('DELETE IGNORE FROM users WHERE uuid=?', [$uuid]);
    }

    function createRole($role) {
        // создание записи в БД
        $this->run('INSERT IGNORE INTO roles (role) VALUES (?)', [$role]);
    }

    function removeRole($role) {
        // удаление записи
        $this->run('DELETE IGNORE FROM roles WHERE role=?', [$role]);
    }

    function setRoleToUser($role, $uuid) {
        // получение id роли
        $rows = $this->fetch('SELECT `role_id` FROM roles WHERE role=?', [$role]);
        if($rows == false) {
            throw new Exception("Указанная роль '$role' не существует");
        }
        $role_id = intval($rows['role_id']);
        // создание записи в БД
        $this->run(
            'INSERT IGNORE INTO users_roles 
            (uuid, role_id)
            VALUES (:uuid, :role_id)',
            [
                ':uuid' => $uuid,
                ':role_id' => $role_id
            ]
        );
    }

}

?>