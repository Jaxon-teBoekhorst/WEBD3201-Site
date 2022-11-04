<?php
/**
 * This is for my WEBD-3201 course
 * This file handles logging users out from my site
 *
 * This page has no visuals
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(September, 25, 2022)
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
    log_sign_out($email);

    // set message and redirect
    set_message($email . "<br/>has successfully logged out");
}

redirect("./sign-in.php");

require("./includes/footer.php");
