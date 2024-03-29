<?php
/**
 * This is for my WEBD-3201 course
 * This file contains the sign-in page for my site
 *
 * This page has two form inputs that allow users to sign in
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(September, 13, 2022)
 */

// page comments
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$title = "WEBD3201 Login Page";
$author = "Jaxon teBoekhorst";
$date = "13 September 2022";
$file = "./sign-in.php";
$desc = "User sign in";

// header include
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
require_once("./includes/header.php");

// check that a user is signed in
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if (is_logged_in()) {
    redirect("./dashboard.php");
}

// data inputted to a form
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate that data was entered
    if (isset($_POST["inputEmail"]) && isset($_POST["inputPassword"])) {
        // get info from post
        $email = $_POST["inputEmail"];
        $password = $_POST["inputPassword"];

        // clear post
        $_POST["inputEmail"] = "";
        $_POST["inputPassword"] = "";

        // attempt to sign in
        sign_in($email, $password);
    }
}

// display the input form
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
echo display_form(
    [
        "prepended" => "
        <h1 class=\"h3 mb-3 font-weight-normal text-center\">Please sign in</h1>
        <h1 class=\"h4 mb-3 font-weight-normal text-center\">$message</h1>
        ",
        [
            "type" => "email",
            "name" => "inputEmail",
            "value" => "",
            "label" => "Email Address",
            "class" => "form-control",
            "other" => "required autofocus"
        ],
        [
            "type" => "password",
            "name" => "inputPassword",
            "value" => "",
            "label" => "Password",
            "class" => "form-control",
            "other" => "required"
        ],
        "appended" => "
        <button class=\"btn btn-lg btn-primary btn-block\" type=\"submit\">Sign in</button>
        "
    ]
);

echo "
<div class='text-center'>
	<a href='./reset.php' class='btn btn-info'>Reset Password</a>
</div>
";

// include footer
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
require_once("./includes/footer.php");
