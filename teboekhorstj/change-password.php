<?php
/**
 * This is for my WEBD-3201 course
 * This file contains the clients page for my site
 *
 * This page allows salespeople to add a client and view their current clients
 * Admins are able to do everything that salespeople can, though they can select which salesperson it applies to
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(November, 16, 2022)
 */

$title = "WEBD3201 Change Password Page";
$author = "Jaxon teBoekhorst";
$date = "16 November 2022";
$file = "./change-password.php";
$desc = "Allows users to update their passwords";

require_once('includes/header.php');

if(!is_logged_in()){
	set_message("You need to be logged in to access that page");
	redirect('sign-in.php');
}

echo "<h1 class='h4 mb-3 font-weight-normal text-center'>$message</h1>";

// get values from server
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';
$user_id = $_SESSION['user_id'];
$user = $_SESSION["current_user"] ?? '';

// error message for validation
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$valid_password = true;
	if (strlen($password) < PASSWORD_MIN_SIZE || strlen($password) > LONG_MAX_SIZE) {
		$valid_password = false;
		$error_message .= sprintf("Error, Your password must be between %s and %s characters long<br/>", PASSWORD_MIN_SIZE, LONG_MAX_SIZE);
		$password = '';
		$confirm = '';
	}

	if ($password != $confirm) {
		$valid_password = false;
		$error_message .= "Error the two passwords do not match<br/>";
		$password = '';
		$confirm = '';
	}

	if ($valid_password) {
		update_password($password, $user_id);
		log_password_change($user);
		set_message("Password Successfully Updated");
		redirect('./dashboard.php');
	} else {
		$error_message .= "Please try again";
	}
}

echo display_form([
	[
		'type' => 'password',
		'name' => 'password',
		'label' => 'New Password',
		"class" => "form-control",
		"other" => "required autofocus"
	],
	[
		'type' => 'password',
		'name' => 'confirm',
		'label' => 'Confirm Password',
		"class" => "form-control",
		"other" => "required"
	],
	"appended" => "
	<button class='btn btn-lg btn-primary btn-block' type='submit'>Change Password</button>"
]);

// display any error messages
echo "<p class='h6 font-weight-bold text-center'>$error_message</p>";

require_once('includes/footer.php');