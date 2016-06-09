<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set("display_errors", 1);

function showError()
{
    die("ERROR: Not supported function");
}

function runQuery($query) {
    $servername = "127.0.0.1";
    $username = "knitomi";
    $password = "********";
    $dbname = "test";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    $sql = $query;
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    echo json_encode($data);
    $conn->close();
}

function getUsers()
{
    $sql = "SELECT id, first_name, last_name, birth_date FROM users";
    runQuery($sql);
}

function getGuitars()
{
    $sql = "SELECT id, brand, type, year FROM guitars";
    runQuery($sql);
}

function getFunctions($get, $action)
{
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

if (isset($_GET['action'])) {
    getFunctions($_GET, $_GET['action']);

} else if (isset($_POST['action'])) {
    //nothing yet
} else {
    //throw some error
    showError();
}

