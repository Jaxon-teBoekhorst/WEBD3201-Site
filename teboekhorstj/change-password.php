<?php
require_once('includes/header.php');

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

echo display_form([
	[
		'type' => 'password',
		'name' => 'password',
		'label' => 'New Password',
		'value' => '',
		"class" => "form-control",
		"other" => "required autofocus"
	],
	[
		'type' => 'password',
		'name' => 'confirm',
		'label' => 'Confirm Password',
		'value' => '',
		"class" => "form-control",
		"other" => "required"
	],
	"appended" => "
	<button class='btn btn-lg btn-primary btn-block' type='submit'>Change Password</button>"
]);


require_once('includes/footer.php');