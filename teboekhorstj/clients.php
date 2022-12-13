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
 * @version 1.0(September, 13, 2022)
 */

// page comments
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$title = "WEBD3201 Calls Page";
$author = "Jaxon teBoekhorst";
$date = "04 October 2022";
$file = "./clients.php";
$desc = "display and create new clients for salespeople";

// header include
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
require_once("./includes/header.php");

// get userinfo from server
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$user = $_SESSION["current_user"] ?? "";
$user_type = $user != "" ? $_SESSION["user_type"] : "";
$user_id = $user != "" ? $_SESSION["user_id"] : "";

// check if user has permission to be on this page
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if (!($user_type == 'a' || $user_type == 's')) {
	set_message('Sorry, You do not have permission to use this page');
	redirect('./sign-in.php');
}

// display a message
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
echo "<h1 class='h4 mb-3 font-weight-normal text-center'>$message</h1>";

// get data from server
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// post values
$client_f_name = $_POST['client_f_name'] ?? '';
$client_l_name = $_POST['client_l_name'] ?? '';
$client_email = $_POST['client_email'] ?? '';
$client_phone_num = $_POST['client_phone_num'] ?? '';
$client_phone_ext = $_POST['client_phone_ext'] ?? '';
$page = $_POST['page'] ?? 1;

// get file info
$logo_name = $_FILES['logo_name']['name'] ?? '';
$logo_path = $_FILES['logo_name']['tmp_name'] ?? '';
$logo_error = $_FILES['logo_name']['error'] ?? '';
$logo_size = $_FILES['logo_name']['size'] ?? '';
$logo_ext = pathinfo($logo_name, PATHINFO_EXTENSION);

// get salesperson info (selected salesperson for admins)
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
if ($user_type == 'a') {
	// get selected user and their id
	$current_salesperson = $_SESSION['selected_salesperson'] ?? 'jax.tebs+webd3201salesperson@outlook.com';
	$current_user_id = get_user_id($current_salesperson);
	$current_user_id = pg_fetch_result($current_user_id, '0', 'Id');
} else {
	$current_salesperson = $user;
	$current_user_id = $user_id;
}

// data entered in form
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// error message for validation
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // select salesperson button
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	if (isset($_POST['btnSalesperson'])) {
		$current_salesperson = $_POST['salesperson'];
		$_SESSION['selected_salesperson'] = $current_salesperson;
		$current_user_id = get_user_id($current_salesperson);
		$current_user_id = pg_fetch_result($current_user_id, '0', 'Id');
    // page select button (all logic handled elsewhere)
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
	} else if (isset($_POST['btnPage'])) {
	// create new client button
    // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    } else {
        // get selected salesperson info if user is an admin
		if ($user_type == 'a') {
			// get selected user and their id
			$current_salesperson = $_SESSION['selected_salesperson'] ?? 'jax.tebs+webd3201salesperson@outlook.com';
			$current_user_id = get_user_id($current_salesperson);
			$current_user_id = pg_fetch_result($current_user_id, '0', 'Id');
		}

		// validate all fields
		$valid_client = true;
		if (
			$client_f_name == '' ||
			$client_l_name == '' ||
			$client_email == ''
		) {
			$valid_client = false;
			$error_message .= "Please fill all fields<br/>";
		}

        // validate field lengths
		if (strlen($client_f_name) > 128) {
			$valid_salesperson = false;
			$error_message .= "Error: First Name too long<br/>";
		}
		if (strlen($client_l_name) > 128) {
			$valid_salesperson = false;
			$error_message .= "Error: Last Name too long<br/>";
		}
		if (strlen($client_email) > 255) {
			$valid_salesperson = false;
			$error_message .= "Error: Email too long<br/>";
		}

        // check for valid email
		if (!filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
			$valid_client = false;
			$client_email = '';
			$error_message .= "Error: Invalid Email Address<br/>";
		}

		// validate phone number
		$phone_num = preg_replace("/[^0-9]/", "", $client_phone_num);
		if (strlen($phone_num) != 10) {
			$valid_client = false;
			$client_phone_num = '';
			$error_message .= "Error: Invalid Phone Number<br/>";
		}

		// validate extension
		$phone_ext = preg_replace("/[^0-9]/", "", $client_phone_ext);

		// check if the client already exists
		if (check_for_client($client_email)) {
			$valid_client = false;
			$error_message .= "A client already exists with that email<br/>";
		}

        // check for valid file upload (no type check yet)
		if ($logo_error != 0) {
			$valid_client = false;
			$error_message .= "An error has occurred when trying to upload the logo<br/>";
		}

        // check for valid file type
		if (!in_array($logo_ext, ACCEPTED_FILE_TYPES)){
			$valid_client = false;
			$error_message .= 'The selected file is not an accepted file type<br/>';
		}

        // check for valid file size
		if ($logo_size > MAX_FILE_SIZE) {
			$valid_client = false;
			$error_message .= sprintf("The selected file is too large, The max size is %s<br/>", MAX_SIZE_STR);
		}

		if ($valid_client) {
			// Store uploaded logo file if it exists
			$stored_path = '';
			if ($logo_name != '') {
				$stored_path = "./uploads/$logo_name";
				move_uploaded_file($logo_path, $stored_path);
			}

			// add client to the database
			if (!add_client($client_f_name, $client_l_name, $client_email, $client_phone_num, $client_phone_ext, $current_user_id, $stored_path)) {
				$error_message = "Could not successfully add $client_f_name $client_l_name to the database";
			} else {
				set_message("Successfully added $client_f_name $client_l_name to the database");
				redirect('clients.php');
			}
		}
	}
}

// render select salesperson if the user is an admin
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// check if the user is an admin
if ($user_type == 'a') {
	// get a list of salespeople emails
	$result = get_sales_people();
	$salespeople = [];
	for ($i = 0; $i < pg_num_rows($result); $i++) {
		$salesperson = pg_fetch_row($result, $i);
		$salespeople[] = $salesperson[1];
	}

	// generate dropdown selections
	$dropdown = "<select class='form-control mb-auto' style='width: auto; height: auto' name='salesperson'>";
	foreach ($salespeople as $salesperson) {
		$dropdown .= "<br/>";

		if ($current_salesperson == $salesperson) {
			$dropdown .= "<option value='$salesperson' selected='selected'>$salesperson</option>";
		} else {
			$dropdown .= "<option value='$salesperson'>$salesperson</option>";
		}


	}
	$dropdown .= "</select>";

	// drop down selection
	echo "<p class='h3 mb-3 font-weight-normal text-center'>Select Salesperson</p>";
	echo display_form([
		"appended" => $dropdown .
			"<button class='btn btn-lg btn-primary btn-block mt-3 mb-5' name='btnSalesperson' type='submit'>Show Clients</button>"
	]);
}


// display current clients in a paged table
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
$clients = get_clients($current_user_id);

if (pg_num_rows($clients) == 0) {
	if ($user_type == 'a') {
		echo "<p class='h4 text-center mb-5'>The selected salesperson does not have any clients</p>";
	} else {
		echo "<p class='h4 text-center mb-5'>You have no clients</p>";
	}
} else {
	echo "<p class='h5'>Clients</p>";
	echo display_table(
		[
			[
				"email" => "Email",
				"firstName" => "First Name",
				"lastName" => "Last Name",
				"phoneNum" => "Phone Number",
				"phoneExt" => "Phone Extension",
				"logoPath" => "Logo"
			],
			$clients,
			pg_num_rows($clients),
			$page
		]
	);

	// display page selector
	echo "<form class='form-control border-0' method='POST'>
			<p>Page:</p>
			<select class='form-control mb-auto' style='width: auto; height: auto' name='page'>";

	// generate all selections
	for ($i = 0;
		 $i <= floor(((pg_num_rows($clients) - 1) / RESULTS_PER_PAGE));
		 $i++) {
		$selected_page = $i + 1;
		echo "<option value='$selected_page'>$selected_page</option>";
	}

	echo "</select>
	<button class='btn btn-primary' type='submit' name='btnPage'>Submit</button>
	</form>";
}

// Create new clients
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
echo "<h2 class='h3 mb-3  mt-5 font-weight-normal text-center'> Create New Client</h2>";
echo display_form(
	[
		[
			"type" => "text",
			"name" => "client_f_name",
			"value" => $client_f_name,
			"label" => "First Name",
			"class" => "form-control",
			"other" => "required autofocus"
		],
		[
			"type" => "text",
			"name" => "client_l_name",
			"value" => $client_l_name,
			"label" => "Last Name",
			"class" => "form-control",
			"other" => "required"
		],
		[
			"type" => "email",
			"name" => "client_email",
			"value" => $client_email,
			"label" => "Client Email",
			"class" => "form-control",
			"other" => "required"
		],
		[
			"type" => "number",
			"name" => "client_phone_num",
			"value" => $client_phone_num,
			"label" => "Client Phone Number",
			"class" => "form-control",
			"other" => "required"
		],
		[
			"type" => "number",
			"name" => "client_phone_ext",
			"value" => $client_phone_ext,
			"label" => "Phone Number Ext. ",
			"class" => "form-control"
		],
		[
			"type" => "file",
			"class" => "form-control",
			"name" => "logo_name"
		],
		"appended" => "
		<label class='form-text'>Select Client Logo</label>
		<button class='btn btn-lg btn-primary btn-block' type='submit'>Create Client</button>
        "
	]
);

echo "<p class='h6 font-weight-bold text-center'>$error_message</p>";

// footer include
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
require_once('includes/footer.php');