<?php
/*
 * Jaxon teBoekhorst
 * 04 October 2022
 * WEBD3201  
 */

$title = "WEBD3201 Calls Page";
$author = "Jaxon teBoekhorst";
$date = "04 October 2022";
$file = "./salespeople.php";
// TODO description
$desc = "";

require_once("./includes/header.php");

$user = isset($_SESSION["current_user"]) ? isset($_SESSION["current_user"]) : "";
$user_type = $user != "" ? $_SESSION["user_type"] : '';

if ($user_type != 'a') {
    setMessage('Sorry, You do not have permission to use this page');
    redirect('./sign-in.php');
}
