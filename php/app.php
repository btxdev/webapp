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

        //var_dump($decoded);

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

        

        //$username = $db->strgen();
        $username = $decoded['username'];
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

        //echo(123);

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
            WHERE `employee_id` = :id',
            [
                ':username' => $username,
                ':first_name' => $first_name,
                ':second_name' => $second_name,
                ':patronymic' => $patronymic,
                ':phone' => $phone,
                ':email' => $email,
                ':id' => $id
            ]
        );

        $res = new Status('OK');
        exit($res->json());

    }

    if($decoded['op'] == 'get_employees') {
        $rows = $db->fetchAll("SELECT * FROM `employees`");
        $users = [];
        foreach ($rows as $row) {
            $users[$row['employee_id']] = [
                'id' => $row['employee_id'],
                'first_name' => $row['first_name'],
                'second_name' => $row['second_name'],
                'patronymic' => $row['patronymic'],
                'position' => null,
            ];
        }
        $rows = $db->fetchAll("SELECT employee_id, position FROM `positions_employees` INNER JOIN `positions` WHERE positions_employees.position_id = positions.position_id");
        foreach ($rows as $row) {
            $users[$row['employee_id']]['position'] = $row['position'];
        }
        $result = new Status('OK', ['msg' => $users]);
        exit($result->json());
    }

    // для select option
    if($decoded['op'] == 'get_employee_options') {
        // get roles
        $roles = $db->fetchAll("SELECT * FROM `roles`");
        // get positions
        $positions = $db->fetchAll("SELECT * FROM `positions`");
        //
        $data = [
            'status' => 'OK',
            'response' => [
                'roles' => $roles,
                'positions' => $positions
            ]
        ];
        echo(json_encode($data));
        exit();
    }

    // === услуги ===

    if($decoded['op'] == 'get_services') {
        $rows = $db->fetchAll('SELECT * FROM `services`');
        $data = [
            'status' => 'OK',
            'services' => $rows
        ];
        exit(json_encode($data));
    }

    if($decoded['op'] == 'get_services_data') {
        requireFields(['service_id']);
        $service = $db->fetch('SELECT * FROM `services` WHERE `service_id` = :service_id', [':service_id' => $decoded['service_id']]);
        $data = [
            'status' => 'OK',
            'service' => $service
        ];
        exit(json_encode($data));
    }

    if($decoded['op'] == 'add_service') {
        requireFields(['service', 'description', 'price']);
        $db->run('
            INSERT INTO `services` 
            (service, description, price) 
            VALUES (:service, :description, :price)', 
            [
                ':service' => $decoded['service'],
                ':description' => $decoded['description'],
                ':price' => $decoded['price']
            ]);
        $result = new Status('OK');
        exit($result->json());
    }

    if($decoded['op'] == 'edit_service') {
        requireFields(['service_id', 'service', 'description', 'price']);
        $db->run('
            UPDATE `services` 
            SET `service` = :service, 
            `description` = :description, 
            `price` = :price 
            WHERE `service_id` = :service_id', 
            [
                ':service_id' => $decoded['service_id'],
                ':service' => $decoded['service'],
                ':description' => $decoded['description'],
                ':price' => intval($decoded['price'])
            ]
        );
        $result = new Status('OK');
        exit($result->json());
    }

    if($decoded['op'] == 'remove_service') {
        requireFields(['service_id']);
        $db->run('
            DELETE FROM `services` 
            WHERE service_id = :service_id', 
            [
                ':service_id' => $decoded['service_id']
            ]
        );
        $result = new Status('OK');
        exit($result->json());
    }

    // === недвижимость ===

    if($decoded['op'] == 'get_apartments') {
        $rows = $db->fetchAll('SELECT * FROM `apartments`');
        $data = [
            'status' => 'OK',
            'apartments' => $rows
        ];
        exit(json_encode($data));
    }

    // if($decoded['op'] == 'get_apartments_data') {
    //     requireFields(['apartment_id']);
    //     $apartment = $db->fetch('SELECT * FROM `apartments` WHERE `apartment_id` = :apartment_id', [':apartment_id' => $decoded['apartment_id']]);
    //     $data = [
    //         'status' => 'OK',
    //         'service' => $service
    //     ];
    //     exit(json_encode($data));
    // }

    if($decoded['op'] == 'add_apartment') {
        requireFields(['address', 'company', 'date', 'price']);
        $db->run('
            INSERT INTO `apartments` 
            (type, address, developer, construction_date, publication_date, price) 
            VALUES (:type, :address, :developer, :construction_date, :publication_date, :price)', 
            [
                ':type' => 'default',
                ':address' => $decoded['address'],
                ':developer' => $decoded['company'],
                ':construction_date' => $decoded['date'],
                ':publication_date' => date('Y-m-d'),
                ':price' => $decoded['price']
            ]);
        $result = new Status('OK');
        exit($result->json());
    }

    // if($decoded['op'] == 'edit_service') {
    //     requireFields(['service_id', 'service', 'description', 'price']);
    //     $db->run('
    //         UPDATE `services` 
    //         SET `service` = :service, 
    //         `description` = :description, 
    //         `price` = :price 
    //         WHERE `service_id` = :service_id', 
    //         [
    //             ':service_id' => $decoded['service_id'],
    //             ':service' => $decoded['service'],
    //             ':description' => $decoded['description'],
    //             ':price' => intval($decoded['price'])
    //         ]
    //     );
    //     $result = new Status('OK');
    //     exit($result->json());
    // }

    if($decoded['op'] == 'remove_apartment') {
        requireFields(['apartment_id']);
        $db->run('
            DELETE FROM `apartments` 
            WHERE apartment_id = :apartment_id', 
            [
                ':apartment_id' => $decoded['apartment_id']
            ]
        );
        $result = new Status('OK');
        exit($result->json());
    }

    // сделки

    // для select option
    if($decoded['op'] == 'get_deals_options') {
        // get services
        $services = [];
        $arr = $db->fetchAll("SELECT * FROM `services`");
        foreach($arr as $item) {
            array_push($services, [
                'id' => $item['service_id'],
                'description' => $item['service']
            ]);
        }
        // get apartments
        $apartments = [];
        $arr = $db->fetchAll("SELECT * FROM `apartments`");
        foreach($arr as $item) {
            array_push($apartments, [
                'id' => $item['apartment_id'],
                'description' => $item['address'].' ('.$item['developer'].')'
            ]);
        }
        //
        $data = [
            'status' => 'OK',
            'response' => [
                'services' => $services,
                'apartments' => $apartments
            ]
        ];
        echo(json_encode($data));
        exit();
    }

    if($decoded['op'] == 'get_deals') {
        $rows = $db->fetchAll('SELECT * FROM `deals`');
        $deals = [];
        foreach($rows as $item) {
            $client_id = $db->fetch(
                "SELECT `client_id` 
                FROM `clients_deals` 
                WHERE `deal_id` LIKE :deal_id",
                [
                    ':deal_id' => $item['deal_id']
                ]
            );
            if($client_id == false) continue;
            else $client_id  = $client_id['client_id'];
            $client_data = $db->fetch(
                "SELECT * 
                FROM `clients` 
                WHERE `client_id` LIKE :client_id",
                [
                    ':client_id' => $client_id
                ]
            );
            $client_n1 = $client_data['second_name'];
            $client_n2 = $client_data['first_name'];
            if(mb_strlen($client_n2) == 0) $client_n2 = '#';
            $client_n3 = $client_data['patronymic'];
            if(mb_strlen($client_n3) == 0) $client_n3 = '#';
            $client_fullname = $client_n1.' '.$client_n2.'. '.$client_n3.'.';

            $employee_id = $db->fetch(
                "SELECT `employee_id` 
                FROM `employees_deals` 
                WHERE `deal_id` LIKE :deal_id",
                [
                    ':deal_id' => $item['deal_id']
                ]
            );
            if($employee_id == false) continue;
            else $employee_id  = $employee_id['employee_id'];
            $client_data = $db->fetch(
                "SELECT * 
                FROM `employees` 
                WHERE `employee_id` LIKE :employee_id",
                [
                    ':employee_id' => $employee_id
                ]
            );
            $emp_n1 = $client_data['second_name'];
            $emp_n2 = $client_data['first_name'];
            if(mb_strlen($emp_n2) == 0) $emp_n2 = '#';
            $emp_n3 = $client_data['patronymic'];
            if(mb_strlen($emp_n3) == 0) $emp_n3 = '#';
            $employee_fullname = $emp_n1.' '.$emp_n2.'. '.$emp_n3.'.';

            $contract_id = $db->fetch(
                "SELECT `contract_id` 
                FROM `contracts_deals` 
                WHERE `deal_id` LIKE :deal_id",
                [
                    ':deal_id' => $item['deal_id']
                ]
            );
            if($contract_id == false) continue;
            else $contract_id  = $contract_id['contract_id'];

            array_push($deals, [
                'deal_id' => $item['deal_id'],
                'deal' => $item['deal'],
                'deal_date' => $item['deal_date'],
                'client' => $client_fullname,
                'employee' => $employee_fullname,
                'contract_id' => $contract_id
            ]);
        }

        $data = [
            'status' => 'OK',
            'deals' => $deals
        ];
        exit(json_encode($data));
    }

    if($decoded['op'] == 'add_deal') {

        $current_uuid = intval($current_uuid);

        // deal -> deals.deal
        $deal = $decoded['deal'];
        // deal.date = date('Y-m-d')
        $deal_date = date('Y-m-d');
        // create deal
        $db->run("
            INSERT INTO `deals`
            (deal, deal_date)
            VALUES (:deal, :deal_date)
            ",
            [
                ':deal' => $deal,
                ':deal_date' => $deal_date
            ]
        );
        // get deal_id
        $deal_id = $db->instance->lastInsertId();
        if($deal_id == false) {
            $result = new Status('WRONG_FORMAT');
            exit($result->json());
        }

        // employees_deals.employee_id = current_uuid
        // employees_deals.deal_id = deal_id
        $db->run(
            "INSERT  INTO `employees_deals`
            (`employee_id`, `deal_id`)
            VALUES (:employee_id, :deal_id)",
            [
                ':employee_id' => $current_uuid,
                ':deal_id' => $deal_id
            ]
            );

        // services_deals.service_id = service_id
        // services_deals.deal_id = deal_id
        $db->run(
            "INSERT INTO `services_deals`
            (`service_id`, `deal_id`)
            VALUES (:service_id, :deal_id)",
            [
                ':service_id' => $decoded['service_id'],
                ':deal_id' => $deal_id
            ]
            );

        // create client
        $db->run(
            "INSERT INTO `clients`
            (`first_name`, `second_name`, `patronymic`, `birth_date`, `phone`, `email`, `reg_date`)
            VALUES (:first_name, :second_name, :patronymic, :birth_date, :phone, :email, :reg_date)",
            [
                ':first_name' => $decoded['client_name1'],
                ':second_name' => $decoded['client_name2'],
                ':patronymic' => $decoded['client_name3'],
                ':birth_date' => $decoded['client_birth'],
                ':phone' => $decoded['client_phone'],
                ':email' => $decoded['client_email'],
                ':reg_date' => date('Y-m-d')
            ]
            );
        // get client_id
        $client_id = $db->instance->lastInsertId();
        if($client_id == false) {
            $result = new Status('WRONG_FORMAT');
            exit($result->json());
        }
        // client_deals.client_id = client_id
        // client_deals.deal_id = deal_id
        $db->run(
            "INSERT INTO `clients_deals`
            (`client_id`, `deal_id`)
            VALUES (:client_id, :deal_id)",
            [
                ':client_id' => $client_id,
                ':deal_id' => $deal_id
            ]
            );

        // create contract
        $db->run(
            "INSERT INTO `contracts`
            (`type`, `date`)
            VALUES (:type, :date)",
            [
                ':type' => 'default',
                ':date' => date('Y-m-d')
            ]
            );
        // get contract_id
        $contract_id = $db->instance->lastInsertId();
        if($contract_id == false) {
            $result = new Status('WRONG_FORMAT');
            exit($result->json());
        }
        // contracts_deals.contract_id = contract_id
        // contracts_deals.deal_id = deal_id
        $db->run(
            "INSERT INTO `contracts_deals`
            (`contract_id`, `deal_id`)
            VALUES (:contract_id, :deal_id)",
            [
                ':contract_id' => $contract_id,
                ':deal_id' => $deal_id
            ]
            );

        // apartments_deals.apartment_id = apartment_id
        // apartments_deals.deal_id = deal_id
        $db->run(
            "INSERT INTO `apartments_deals`
            (`apartment_id`, `deal_id`)
            VALUES (:apartment_id, :deal_id)",
            [
                ':apartment_id' => $decoded['apartment_id'],
                ':deal_id' => $deal_id
            ]
            );

        $result = new Status('OK');
        exit($result->json());
    }

    if($decoded['op'] == 'remove_deal') {
        requireFields(['deal_id']);
        $db->run('
            DELETE FROM `deals` 
            WHERE deal_id = :deal_id', 
            [
                ':deal_id' => $decoded['deal_id']
            ]
        );
        $result = new Status('OK');
        exit($result->json());
    }

}





?>