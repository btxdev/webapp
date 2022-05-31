<?php

include_once __DIR__.'/main.php';
include_once __DIR__.'/include_db.php';

$decoded = [];

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if ($contentType === "application/json") {
    //Receive the RAW post data.
    $content = trim(file_get_contents("php://input"));

    $decoded = json_decode($content, true);

    //If json_decode failed, the JSON is invalid.
    if(! is_array($decoded)) {
        exit(emptyJson());
    }
    else {
        //var_dump($decoded);
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
        if(!isset($decoded[$field])) exit(emptyJson());
    }
}

if(isset($decoded['op'])) {

    if($decoded['op'] == 'login') {

        // $session = $access->getSessionCookie($settings->get('session_name'));
        // if($session != 'none') {
        //     $id = $access->getUserIdBySessionHash($session);
        //     if($id != false) {
        //         // authorized
        //         exit('AUTHORIZED');
        //     }
        // }

        requireFields(['login', 'password']);

        $login = processStatus($validate->login($decoded['login']));
        $password = processStatus($validate->password($decoded['password']));
        
        $loginResult = $access->login($login, $password);
        if($loginResult->ok()) {
            $hash = $loginResult->session;
            $result = $access->setSessionCookie($settings->get('session_name'), $hash);
            exit($result->json());
        }
        else {
            exit($loginResult->json());
        }
    }

    if($decoded['op'] == 'register') {
        
    }

    if($decoded['op'] == 'logout') {
        $session = $access->getSessionCookie($settings->get('session_name'));
        if($session != 'none') {
            $id = $access->getUserIdBySessionHash($session);
            if($id != false) {
                // authorized
                try {
                    $access->removeAccessFrom($session);
                    $status = new Status('OK');
                }
                catch(Exception $e) {
                    $status = new Status('ERROR');
                }
                exit($status->json());
            }
        }
        exit(emptyJson());
    }

}

?>