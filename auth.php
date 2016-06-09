<?php

define("AUTHKEY", "QWERASDFYXCV");//for testing

function loggedIn(){
    return empty($_SESSION['Auth']) ? false : true;
}

function logout(){
    unset($_SESSION['Auth']);
    session_destroy();
}

function validAuthKey($key){
    return $key == AUTHKEY;
}