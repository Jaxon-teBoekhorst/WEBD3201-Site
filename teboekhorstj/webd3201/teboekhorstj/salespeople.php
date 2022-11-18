<?php
/**
 * This is for my WEBD-3201 course
 * This file contains the salespeople page for my site
 *
 * This page allows admins to add salespeople to the user table in the site database
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(October, 04, 2022)
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
	set_message('Sorry, You do not have permission to use this page');
	redirect('./sign-in.php');
}

$f_name = $_POST['f_name'] ?? '';
$l_name = $_POST['l_name'] ?? '';
$email = $_POST['email'] ?? '';
$page = $_POST['page'] ?? 1;
$password = '';
$error_message = '';

echo "<h1 class='h4 mb-3 font-weight-normal text-center'>$message</h1>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['btnPage'])) {
	} else {
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
				set_message("Successfully added $f_name $l_name as a salesperson");
				redirect('salespeople.php');
			}
		} else {
			$password = '';
		}
	}
}

// Create new salesperson
echo "<h2 class='h3 mb-3 font-weight-normal text-center'> Create New Salesperson</h2>";
echo display_form(
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

// display all salespeople in a paged table
$salespeople = get_sales_people();
echo display_table([
	[
		"EmailAddress" => "Email Address",
		"FirstName" => "First Name",
		"LastName" => "Last Name",
		"Id" => "Salesperson ID",
		"LastAccess" => "Last Logged In"
	],
	$salespeople,
	pg_num_rows($salespeople),
	$page
]);

// display page selector
echo "<form class='form-control border-0' method='POST'>
			<p>Page:</p>
			<select class='form-control mb-auto' style='width: auto; height: auto' name='page'>";

// generate all selections
for ($i = 0;
	 $i < floor(((pg_num_rows($salespeople) - 1) / RESULTS_PER_PAGE));
	 $i++) {
	$selected_page = $i + 1;
	echo "<option value='$selected_page'>$selected_page</option>";
}

echo "</select>
	<button class='btn btn-primary' type='submit' name='btnPage'>Submit</button>
	</form>";

require_once('includes/footer.php');