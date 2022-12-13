<?php
/**
 * This is for my WEBD-3201 course
 * This file contains the password reset request page
 *
 * This page has one input and a button, allowing a user to request a new password
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(December, 1, 2022)
 */

$title = "WEBD3201 Reset Password";
$author = "Jaxon teBoekhorst";
$date = "1 December 2022";
$file = "./reset.php";
$desc = "Password Reset Request, Sends an email to reset password to the requested email";

require_once('./includes/header.php');

$email = $_POST['user_email'] ?? '';

// TODO form for email input
echo display_form([
	[
		"type" => "email",
		"name" => "user_email",
		"value" => $email,
		"label" => "Please enter your email",
		"class" => "form-control",
		"other" => "required autofocus"
	],
	"appended" => "<button class='btn btn-primary btn-block'>Submit</button>"
]);

// validate email is valid
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
	// validate the user exists
	if (check_for_user($email)) {
		// if exists log an 'email' for the password
		log_reset_request($email, true);
	} else {
		log_reset_request($email, false);
	}
	// send success message and redirect to sign-in.php
	set_message("Email sent successfully");
	redirect('./sign-in.php');
}

require_once("./includes/footer.php");