<?php

include_once __DIR__.'/main.php';
include_once __DIR__.'/include_db.php';

function processStatus($status) {
    if($status->ok())
        return $status->returnValue;
    else
        exit($status->json());
}
function requireFields($fields) {
    foreach($fields as $field) {
        if(!isset($_POST[$field])) exit();
    }
}

if(isset($_POST['op'])) {

    if($_POST['op'] == 'login') {

        requireFields(['login', 'password']);

        

        $login = processStatus($validate->login($_POST['login']));
        $password = processStatus($validate->password($_POST['password']));
        
        $loginStatus = $access->login($login, $password);
        if($loginStatus->ok()) {
            $hash = $access->grantAccessToUserName($login);
            $result = $access->setSessionCookie($settings->get('session_name'), $hash);
            exit($result->json());
        }
        else {
            exit($loginStatus->json());
        }
    }

    if($_POST['op'] == 'register') {
        
    }

    if($_POST['op'] == 'logout') {
        
    }

}

?>