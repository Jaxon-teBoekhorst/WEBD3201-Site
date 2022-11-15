<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
 */

function db_connect()
{
    return pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DATABASE . " user=" . DB_ADMIN . " password=" . DB_PASSWORD);
}

$conn = db_connect();

$user_select = pg_prepare($conn, "user_select", "SELECT * FROM users WHERE EmailAddress = $1");
$get_sales_people = pg_prepare($conn, "get_sales_people", "SELECT * FROM users WHERE Type = 's'");
//$get_clients = pg_prepare($conn, "");

function user_select($email)
{
    $conn = db_connect();
    return pg_execute("user_select", [$email]);
}

function get_sales_people() {
    $conn = db_connect();
    return pg_execute($conn, "get_sales_people", []);
}

//function get_clients($salesperson) {
//    $conn = db_connect();
//    return pg_execute($conn, "get_clients", [$salesperson]);
//}
