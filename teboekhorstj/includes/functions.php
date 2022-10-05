<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
 */

// redirect page and flush output buffer
function redirect($url)
{
    header("Location:$url");
    ob_flush();
}

// send a message on redirect
function setMessage($message)
{
    $_SESSION['message'] = $message;
}

// retrieve a message
function getMessage()
{
    return $_SESSION['message'];
}

// check if there is a message to display
function isMessage()
{
    return isset($_SESSION['message']);
}

// remove the message from the session variable
function clearMessage()
{
    unset($_SESSION['message']);
}

// flash a message the first time a new page is loaded then clear the message 
function flashMessage()
{
    $message = isMessage() ? getMessage() : "";
    clearMessage();
    return $message;
}

// check if any user is logged in
function isLoggedIn()
{
    return !$_SESSION["current_user"] == "";
}

// sign a user in if the email and password match a database entry
function signIn($email, $password)
{
    $user = user_select($email);

    if (pg_num_rows($user) == 1) {

        $user_email = pg_fetch_result($user, 0, "EmailAddress");
        $user_pass = pg_fetch_result($user, 0, "Password");

        if ($email == $user_email && password_verify($password, $user_pass)) {
            $_SESSION["current_user"] = $user_email;

            $user_fname = pg_fetch_result($user, 0, "FirstName");
            $user_lname = pg_fetch_result($user, 0, "LastName");

            setMessage("Welcome " . $user_fname . " " . $user_lname);
            logSignIn($email, true);
            redirect("./dashboard.php");
        } else {
            setMessage("Incorrect email or password");
            logSignIn($email, false);
            redirect("sign-in.php");
        }
    } else {
        setMessage("No user found");
        logSignIn($email, false);
        redirect("sign-in.php");
    }
}

function logSignIn($email, $success)
{
    $file_name = "./logs/" . date("Ymd") . "_log.txt";
    $message = $success ?
        "Sign in success at " . date("h:ia") . ". User " . $email . " sign in. \n" :
        "Sign in failed at " . date("h:ia") . ". Email provided " . $email . "\n";

    $file = fopen($file_name, "a");
    fwrite($file, $message);
    fclose($file);
}

function logSignOut($email)
{
    $file_name = "./logs/" . date("Ymd") . "_log.txt";
    $message = $email . "succcessfully signed out at " . date("h:ia") . "\n";

    $file = fopen($file_name, "a");
    fwrite($file, $message);
    fclose($file);
}
