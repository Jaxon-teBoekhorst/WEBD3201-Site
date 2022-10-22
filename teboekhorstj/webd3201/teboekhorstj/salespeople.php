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
$desc = "Allow administrators to add new salespeople to the database";

require_once("./includes/header.php");

$user = isset($_SESSION["current_user"]) ? isset($_SESSION["current_user"]) : "";
$user_type = $user != "" ? $_SESSION["user_type"] : '';

if ($user_type != 'a') {
	setMessage('Sorry, You do not have permission to use this page');
	redirect('./sign-in.php');
}

$f_name = $_POST['f_name'] ?? '';
$l_name = $_POST['l_name'] ?? '';
$email = $_POST['email'] ?? '';
$password = '';
$error_message = '';

echo "<h1 class='h4 mb-3 font-weight-normal text-center'>$message</h1>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$password = $_POST['password'];

	// validate fields
	$valid_salesperson = true;
	if (
		$f_name == '' ||
		$l_name == '' ||
		$email == ''
	) {
		$valid_salesperson = false;
		$error_message .= "Please fill all fields<br/>";
	}

	if (strlen($f_name) > 128) {
		$valid_salesperson = false;
		$error_message .= "Error: First Name too long<br/>";
	}
	if (strlen($l_name) > 128) {
		$valid_salesperson = false;
		$error_message .= "Error: Last Name too long<br/>";
	}
	if (strlen($email) > 255) {
		$valid_salesperson = false;
		$error_message .= "Error: Email too long<br/>";
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$valid_salesperson = false;
		$email = '';
		$error_message .= "Error: Invalid Email Address<br/>";
	}

	if (strlen($password) < 8 || strlen($password) > 255) {
		$valid_salesperson = false;
		$error_message .= "The password must be between 8 and 255 characters long<br/>";
		$password = '';
	}

	if (check_for_salesperson($email)) {
		$valid_salesperson = false;
		$error_message .= " A salesperson with that  email already exists<br/>";
	}

	if ($valid_salesperson) {
		if (!add_salesperson($f_name, $l_name, $email, $password)) {
			$error_message .= "Failed to add user $f_name $l_name to the database";
		} else {
			setMessage("Successfully added $f_name $l_name as a salesperson");
			redirect('salespeople.php');
		}
	} else {
		$password = '';
	}
}

// Create new salesperson
echo "<h2 class='h3 mb-3 font-weight-normal text-center'> Create New Salesperson</h2>";
echo displayForm(
	[
		[
			"type" => "text",
			"name" => "f_name",
			"value" => $f_name,
			"label" => "First Name",
			"class" => "form-control",
			"other" => "required autofocus"
		],
		[
			"type" => "text",
			"name" => "l_name",
			"value" => $l_name,
			"label" => "Last Name",
			"class" => "form-control",
			"other" => "required"
		],
		[
			"type" => "email",
			"name" => "email",
			"value" => $email,
			"label" => "Email",
			"class" => "form-control",
			"other" => "required"
		],
		[
			"type" => "password",
			"name" => "password",
			"value" => $password,
			"label" => "Password",
			"class" => "form-control",
			"other" => "required"
		],
		"appended" => "
        <button class='btn btn-lg btn-primary btn-block' type='submit'>Create Salesperson</button>
        "
	]
);

echo "<p class='h6 font-weight-bold text-center'>$error_message</p>";

require_once('includes/footer.php');