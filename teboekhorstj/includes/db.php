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

function user_select($email)
{
    $conn = db_connect();
    return pg_execute($conn, "user_select", array($email));
}
