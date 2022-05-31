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
            'SELECT `employee_id` FROM employees WHERE username=?',
            [$username]
        );
        if($rows == false) return false;
        else {
            return intval($rows['employee_id']);
        }
    }

    function createUser($username, $password) {
        // игнор, если пользователь уже существует
        if($this->getUserId($username) != false) {
            return false;
        }
        $salt = $this->salt();
        // создание записи в БД
        $this->run(
            'INSERT INTO employees 
            (username, password, salt, reg_date)
            VALUES (:username, :password, :salt, :reg_date)',
            [
                ':username' => $username,
                ':password' => $this->hashPassword($password, $salt),
                ':salt' => $salt,
                ':reg_date' => date('Y-m-d')
            ]
        );
        // id созданного пользователя
        $id = $this->getUserId($username);
        return $id;
    }

    function hashPassword($password, $salt) {
        return password_hash($password.$salt.$this->pepper(), PASSWORD_BCRYPT);
    }

    function removeUser($uuid) {
        // удаление записи
        $this->run('DELETE IGNORE FROM employees WHERE employee_id=?', [$uuid]);
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
            'INSERT IGNORE INTO employees_roles 
            (employee_id, role_id)
            VALUES (:employee_id, :role_id)',
            [
                ':employee_id' => $uuid,
                ':role_id' => $role_id
            ]
        );
    }

}

?>