<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);

require_once('auth.php');

function showError() {
    die("ERROR: Not supported function");
}

function getConnection(){
    $servername = "127.0.0.1";
    $username = "knitomi";
    $password = "********";
    $dbname = "test";
    $conn = new mysqli($servername, $username, $password, $dbname);
    return $conn;
}

function runInsertQuery($query) {
    $response["success"] = false;
    $conn = getConnection();
    if ($conn->connect_error) {
        $response["error"] = "Connection failed: " . $conn->connect_error;
    } else {
        $conn->set_charset("utf8");
        $result = $conn->query($query);
        if(!$result){
            $response["error"] = "Insert failed: " . $conn->error;
        } else {
            $response["success"] = true;
            $response['message'] = "Insert successful, new id: " . $conn->insert_id;
        }
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    $conn->close();
}

function runSelectQuery($query){
    $response["success"] = false;
    $conn = getConnection();
    if ($conn->connect_error) {
        $response["error"] = "Connection failed: " . $conn->connect_error;
    } else {
        $conn->set_charset("utf8");
        $result = $conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $response["success"] = true;
        $response["data"] = $data;
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    $conn->close();
}

function getUsers(){
    $sql = "SELECT id, first_name, last_name, birth_date FROM users";
    runSelectQuery($sql);
}

function getGuitars(){
    $sql = "SELECT id, brand, type, year FROM guitars";
    runSelectQuery($sql);
}

function postUsers(){
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $sql = "INSERT INTO users (first_name, last_name, birth_date) VALUES('$first_name','$last_name','$birth_date')";
    runInsertQuery($sql);
}

function getFunctions($action){
    switch ($action) {
        case 'getUsers':
            getUsers();
            break;
        case 'getGuitars':
            getGuitars();
            break;
        default:
            showError();
            break;
    }
}

function postFunctions($action){
    switch ($action) {
        case 'postUsers':
            postUsers();
            break;
        case 'postGuitars':
            //
            break;
        default:
            showError();
            break;
    }
}

if(isset($_GET['authkey']) || isset($_POST['authkey'])){
    $authkey = isset($_GET['authkey']) ? $_GET['authkey'] : $_POST['authkey'];
    if (!validAuthKey($authkey)) {
        die("Invalid auth key!");
    }
} else {
    die("Missing auth key!");
}

if (isset($_GET['action'])) {
    getFunctions($_GET['action']);
} else if (isset($_POST['action'])) {
    postFunctions($_POST['action']);
} else {
    //throw some error
    showError();
}

