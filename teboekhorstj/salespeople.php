<?php
/*
 * Jaxon teBoekhorst
 * 04 October 2022
 * WEBD3201  
 */

$title = "WEBD3201 Calls Page";
$author = "Jaxon teBoekhorst";
$date = "04 October 2022";
$file = "./calls.php";
// TODO description
$desc = "";

require_once("./includes/header.php");

$user = isset($_SESSION["current_user"]) ? isset($_SESSION["current_user"]) : "";

if ($user != "") {
    $user_type = $_SESSION['user_type'];
}

