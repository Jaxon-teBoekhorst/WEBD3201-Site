<?php
/*
 * Jaxon teBoekhorst
 * 25 September 2022
 * WEBD3201  
 */

$title = "WEBD3201 Logout Page";
$author = "Jaxon teBoekhorst";
$date = "25 September 2022";
$file = "./logout.php";
$desc = "Log a user out and redirect to sign-in.php";

require_once("./includes/header.php");

// save email for later use
$email = $_SESSION["current_user"];

// unset and destroy the session
session_unset();
session_destroy();
session_start();

if ($email != "") {
    // log sign out
    logSignOut($email);

    // set message and redirect
    setMessage($email . "<br/>has successfully logged out");
}

redirect("./sign-in.php");

require("./includes/footer.php");
