<?php

    include_once __DIR__.'/main.php';
    include_once __DIR__.'/include_db.php';

    //

    $db->run('SET FOREIGN_KEY_CHECKS = 0;');
    $db->run('SET UNIQUE_CHECKS = 0;');

    // создание сущностей

    // создание таблицы employees
    $db->run('DROP TABLE IF EXISTS `employees`;');
    $db->run(
        'CREATE TABLE `employees` ( 
            `employee_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `username` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,
            `password` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,
            `salt` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,
            `first_name` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL , 
            `second_name` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL , 
            `patronymic` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL , 
            `phone` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL , 
            `email` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL , 
            `reg_date` DATE NOT NULL , 
            PRIMARY KEY (`employee_id`) ,
            UNIQUE `username_index` (`username`(32))
        ) 
        ENGINE = InnoDB'
    );

    // создание таблицы clients
    $db->run('DROP TABLE IF EXISTS `clients`;');
    $db->run(
        'CREATE TABLE `clients` ( 
            `client_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `first_name` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `seconds_name` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `patronymic` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `birth_date` DATE NOT NULL , 
            `phone` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL , 
            `email` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL , 
            `reg_date` DATE NOT NULL , 
            PRIMARY KEY (`client_id`)
        ) 
        ENGINE = InnoDB'
    );

    // создание таблицы roles
    $db->run('DROP TABLE IF EXISTS `roles`;');
    $db->run(
        'CREATE TABLE `roles` ( 
            `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `role` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            PRIMARY KEY (`role_id`)
        ) 
        ENGINE = InnoDB;'
    );

    // создание таблицы positions
    $db->run('DROP TABLE IF EXISTS `positions`;');
    $db->run(
        'CREATE TABLE `positions` ( 
            `position_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `position` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            PRIMARY KEY (`position_id`)
        ) 
        ENGINE = InnoDB;'
    );

    // создание таблицы apartments
    $db->run('DROP TABLE IF EXISTS `apartments`;');
    $db->run(
        'CREATE TABLE `apartments` ( 
            `apartment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `type` INT UNSIGNED NOT NULL , 
            `address` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `developer` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `construction_date` DATE NOT NULL , 
            `publication_date` DATE NOT NULL , 
            `price` INT UNSIGNED NOT NULL , 
            PRIMARY KEY (`apartment_id`)
        ) 
        ENGINE = InnoDB;'
    );

    // создание таблицы contracts
    $db->run('DROP TABLE IF EXISTS `contracts`;');
    $db->run(
        'CREATE TABLE `contracts` ( 
            `contract_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `type` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `date` DATE NOT NULL , 
            PRIMARY KEY (`contract_id`)
        ) 
        ENGINE = InnoDB'
    );

    // создание таблицы passports
    $db->run('DROP TABLE IF EXISTS `passports`;');
    $db->run(
        'CREATE TABLE `passports` ( 
            `passport_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `number` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `birth_location` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `address` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `birth_date` DATE NOT NULL , 
            `organ` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `issue_date` DATE NOT NULL , 
            PRIMARY KEY (`passport_id`)
        ) 
        ENGINE = InnoDB;'
    );

    // создание таблицы services
    $db->run('DROP TABLE IF EXISTS `services`;');
    $db->run(
        'CREATE TABLE `services` ( 
            `service_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `service` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `description` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `price` INT UNSIGNED NOT NULL , 
            PRIMARY KEY (`service_id`)
        ) 
        ENGINE = InnoDB;'
    );

    // создание таблицы deals
    $db->run('DROP TABLE IF EXISTS `deals`;');
    $db->run(
        'CREATE TABLE `deals` ( 
            `deal_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `deal` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `deal_date` DATE NOT NULL , 
            PRIMARY KEY (`deal_id`)
        ) 
        ENGINE = InnoDB;'
    );

    // создание таблицы sessions
    $db->run('DROP TABLE IF EXISTS `sessions`;');
    $db->run(
        'CREATE TABLE `sessions` ( 
            `sesshash` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `employee_id` INT UNSIGNED NOT NULL , 
            `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
            PRIMARY KEY (`sesshash`(32)),
            INDEX `employee_id` (`employee_id`)
        )
        ENGINE = InnoDB;'
    );

    // связь employees и sessions
    $db->run(
        'ALTER TABLE `sessions` ADD FOREIGN KEY (`employee_id`) 
        REFERENCES `employees`(`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;'
    );

    // организация связей

    $admin->relation_1N('roles', 'employees', 'role_id', 'employee_id');
    $admin->relation_1N('positions', 'employees', 'position_id', 'employee_id');
    $admin->relation_1N('employees', 'deals', 'employee_id', 'deal_id');
    $admin->relation_1N('services', 'deals', 'service_id', 'deal_id');
    $admin->relation_11('contracts', 'deals', 'contract_id', 'deal_id');
    $admin->relation_1N('apartments', 'deals', 'apartment_id', 'deal_id');
    $admin->relation_11('clients', 'passports', 'client_id', 'passport_id');
    $admin->relation_1N('clients', 'deals', 'client_id', 'deal_id');

    //

    $db->run('SET FOREIGN_KEY_CHECKS = 1;');
    $db->run('SET UNIQUE_CHECKS = 1;');

    // создание ролей
    $admin->createRole('admin');
    $admin->createRole('default');

    // создание должностей
    $admin->createPosition('Администратор');
    $admin->createPosition('Директор');
    $admin->createPosition('Главный бухгалтер');
    $admin->createPosition('Бухгалтер');
    $admin->createPosition('Агент по недвижимости');

    // создание пользователя admin
    $admin_uuid = $admin->createUser('admin', 'r00tPassw0rd');

    // присвоение роли admin пользователю admin
    $admin->setRoleToUser('admin', $admin_uuid);

    // присвоение должности Администратор пользователю admin
    $admin->setPositionToUser('Администратор', $admin_uuid);

    // создание пользователя test
    $user_uuid = $admin->createUser('test', '123456');

    // присвоение роли default пользователю test
    $admin->setRoleToUser('default', $user_uuid);

    // присвоение должности Агент пользователю test
    $admin->setPositionToUser('Агент по недвижимости', $user_uuid);

    echo('ok');

?>