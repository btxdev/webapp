<?php

include_once __DIR__.'/main.php';
include_once __DIR__.'/include_db.php';

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === "application/json") {
    // получение данных POST формы
    $content = trim(file_get_contents("php://input"));

    $decoded = json_decode($content, true);

    // ошибка обработки JSON
    if(! is_array($decoded)) {
        exit(emptyJson());
    }
}

function emptyJson() {
    return json_encode (json_decode ("{}"));
}
function processStatus($status) {
    if($status->ok())
        return $status->returnValue;
    else
        exit($status->json());
}
function requireFields($fields) {
    global $decoded;
    foreach($fields as $field) {
        if(!isset($decoded[$field])) {
            var_dump($decoded);
            exit(emptyJson());
        }
    }
}

// проверка доступа
$session_name = $settings->get('session_name');
$session_hash = $access->getSessionCookie($session_name);
$current_uuid = $access->getUserIdBySessionHash($session_hash);
if($current_uuid == false) {
    $result = new Status('NOT_AUTHORIZED');
    exit($result->json());
}

if(isset($decoded['op'])) {

    // === базовая информация ===
    if($decoded['op'] == 'basic_info') {

        // получить всю информацию о текущем пользователе
        $rows = $db->fetch(
            'SELECT * FROM `employees` WHERE `employee_id` = :uuid',
            [
                ':uuid' => $current_uuid
            ]
        );
        $data = [
            'status' => 'OK',
            'employee_id' => $current_uuid,
            'username' => $rows['username'],
            'first_name' => $rows['first_name'],
            'second_name' => $rows['second_name'],
            'patronymic' => $rows['patronymic'],
            'phone' => $rows['phone'],
            'email' => $rows['email']
        ];
        exit(json_encode($data));
    }

    // === должности ===

    if($decoded['op'] == 'get_positions') {
        $rows = $db->fetchAll('SELECT * FROM `positions`');
        $data = [
            'status' => 'OK',
            'positions' => $rows
        ];
        exit(json_encode($data));
    }

    if($decoded['op'] == 'get_position_data') {
        requireFields(['position_id']);
        $position = $db->fetch('SELECT `position` FROM `positions` WHERE `position_id` = :position_id', [':position_id' => $decoded['position_id']]);
        $data = [
            'status' => 'OK',
            'position' => $position
        ];
        exit(json_encode($data));
    }

    if($decoded['op'] == 'add_position') {
        requireFields(['position']);
        $db->run('INSERT INTO `positions` (position) VALUES (:position)', [':position' => $decoded['position']]);
        $result = new Status('OK');
        exit($result->json());
    }

    if($decoded['op'] == 'edit_position') {
        requireFields(['position_id', 'position']);
        $db->run('
            UPDATE `positions` 
            SET position = :position 
            WHERE position_id = :position_id', 
            [
                ':position_id' => $decoded['position_id'],
                ':position' => $decoded['position']
            ]
        );
        $result = new Status('OK');
        exit($result->json());
    }

    if($decoded['op'] == 'remove_position') {
        requireFields(['position_id']);
        $db->run('
            DELETE FROM `positions` 
            WHERE position_id = :position_id', 
            [
                ':position_id' => $decoded['position_id']
            ]
        );
        $result = new Status('OK');
        exit($result->json());
    }

    // === сотрудники ===

    if($decoded['op'] == 'add_employee') {

        requireFields([
            'first_name', 
            'second_name', 
            'patronymic', 
            'role_id', 
            'position_id', 
            'birth_date', 
            'phone', 
            'email',
            'username',
            'password'
        ]);

        $username = 'temp_'.$db->strgen();
        $first_name = $decoded['first_name'];
        $second_name = $decoded['second_name'];
        $patronymic = $decoded['patronymic'];
        $role_id = $decoded['role_id'];
        $position_id = $decoded['position_id'];
        $birth_date = $decoded['birth_date'];
        $phone = $decoded['phone'];
        $email = $decoded['email'];
        $password = $decoded['password'];

        $id = $admin->createUser($username, $password);
        if($id == false) {
            $result = new Status('WRONG_FORMAT');
            exit($result->json());
        }

        $admin->setRoleIdToUser($role_id, $id);
        $admin->setPositionIdToUser($position_id, $id);

        $username = 'employee_'.$id;

        // update info about created employee
        $db->run(
            'UPDATE `employees` SET 
            `username` = :username,
            `first_name` = :first_name,
            `second_name` = :second_name,
            `patronymic` = :patronymic,
            `email` = :email,
            `phone` = :phone 
            WHERE `uuid` = :id',
            [
                ':username' => $username,
                ':first_name' => $first_name,
                ':second_name' => $second_name,
                ':patronymic' => $patronymic,
                ':phone' => $phone,
                ':email' => $email
            ]
        );

        $res = new Status('OK');
        exit($res->json());

    }

    if($decoded['op'] == 'get_employees') {
        $rows = $db->fetchAll("SELECT * FROM `employees` INNER JOIN `positions`");
        $users = [];
        foreach ($rows as $row) {
            array_push($users, [
                'id' => $row['employee_id'],
                'first_name' => $row['first_name'],
                'second_name' => $row['second_name'],
                'patronymic' => $row['patronymic'],
                'phone' => $row['phone'],
                'reg_date' => $row['reg_date']
            ]);
        }
        $result = new Status('OK', ['msg' => $users]);
        exit($result->json());
    }

}





?>