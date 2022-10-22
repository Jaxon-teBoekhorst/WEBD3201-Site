<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
 */

$title = "WEBD3201 Login Page";
$author = "Jaxon teBoekhorst";
$date = "13 September 2022";
$file = "./sign-in.php";
$desc = "User sign in";

require_once("./includes/header.php");

if (isLoggedIn()) {
    redirect("./dashboard.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["inputEmail"]) && isset($_POST["inputPassword"])) {
        $email = $_POST["inputEmail"];
        $password = $_POST["inputPassword"];

        $_POST["inputEmail"] = "";
        $_POST["inputPassword"] = "";

        signIn($email, $password);
    }
}

echo displayForm(
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

require "./includes/footer.php";
