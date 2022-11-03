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

$title = "WEBD3201 Calls Page";
$author = "Jaxon teBoekhorst";
$date = "04 October 2022";
$file = "./clients.php";
$desc = "display and create new clients for salespeople";

require_once("./includes/header.php");

$user = $_SESSION["current_user"] ?? "";
$user_type = $user != "" ? $_SESSION["user_type"] : "";
$user_id = $user != "" ? $_SESSION["user_id"] : "";

if (!($user_type == 'a' || $user_type == 's')) {
	setMessage('Sorry, You do not have permission to use this page');
	redirect('./sign-in.php');
}

echo "<h1 class='h4 mb-3 font-weight-normal text-center'>$message</h1>";

// get all values from post
$client_f_name = $_POST['client_f_name'] ?? '';
$client_l_name = $_POST['client_l_name'] ?? '';
$client_email = $_POST['client_email'] ?? '';
$client_phone_num = $_POST['client_phone_num'] ?? '';
$client_phone_ext = $_POST['client_phone_ext'] ?? '';

if ($user_type == 'a') {
	// get selected user and their id
	$current_salesperson = $_SESSION['selected_salesperson'] ?? 'jax.tebs+webd3201salesperson@outlook.com';
	$current_user_id = get_userId($current_salesperson);
	$current_user_id = pg_fetch_result($current_user_id, '0', 'Id');
} else {
	$current_salesperson = $user;
	$current_user_id = $user_id;
}

// client message
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['btnSalesperson'])) {
		$current_salesperson = $_POST['salesperson'];
		$_SESSION['selected_salesperson'] = $current_salesperson;
		$current_user_id = get_userId($current_salesperson);
		$current_user_id = pg_fetch_result($current_user_id, '0', 'Id');
	} else {
		if ($user_type == 'a') {
			// get selected user and their id
			$current_salesperson = $_SESSION['selected_salesperson'] ?? 'jax.tebs+webd3201salesperson@outlook.com';
			$current_user_id = get_userId($current_salesperson);
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

		if (!filter_var($client_email, FILTER_VALIDATE_EMAIL)) {
			$valid_client = false;
			$client_email = '';
			$error_message .= "Error: Invalid Email Address<br/>";
		}

		// validate phone number
		$pnum = preg_replace("/[^0-9]/", "", $client_phone_num);
		if (strlen($pnum) != 10) {
			$valid_client = false;
			$client_phone_num = '';
			$error_message .= "Error: Invalid Phone Number<br/>";
		}

		// validate extension
		$pext = preg_replace("/[^0-9]/", "", $client_phone_ext);

		// check if the client already exists
		if (check_for_client($client_email)) {
			$valid_client = false;
			$error_message .= "A client already exists with that email";
		}

		// add client to the database
		if ($valid_client) {
			if (!add_client($client_f_name, $client_l_name, $client_email, $client_phone_num, $client_phone_ext, $current_user_id)) {
				$error_message = "Could not successfully add $client_f_name $client_l_name to the database";
			} else {
				setMessage("Successfully added $client_f_name $client_l_name to the database");
				redirect('clients.php');
			}
		}
	}
}

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
	echo displayForm([
		"appended" => $dropdown .
			"<button class='btn btn-lg btn-primary btn-block mt-3 mb-5' name='btnSalesperson' type='submit'>Show Clients</button>"
	]);
}


// display current clients
$clients = get_clients($current_user_id);

if (pg_num_rows($clients) == 0) {
	if ($user_type == 'a') {
		echo "<p class='h4 text-center mb-5'>The selected salesperson does not have any clients</p>";
	} else {
		echo "<p class='h4 text-center mb-5'>You have no clients</p>";
	}
} else {

	echo "
<p class='h5'>Clients</p>
<table class='table mb-5'>
<thead>
	<th scope='col'>Id</th>
	<th scope='col'>Name</th>
	<th scope='col'>Email</th>
	<th scope='col'>Phone</th>
</thead>
<tbody>
\n";

	for ($i = 0; $i < pg_num_rows($clients); $i++) {
		$client = pg_fetch_row($clients, $i);

		echo "<tr>\n";
		echo "<th scope='row'>" . $client[0] . "</th>\n";
		echo "<td>" . $client[3] . " " . $client[4] . "</td>\n";
		echo "<td>" . $client[1] . "</td>\n";
		echo "<td>" . formatPhone($client[5]) . " " . $client[6] . "</td>\n";
		echo "<tr>\n";

	}

	echo "
</tbody>
</table> \n";
}
// Create new clients
echo "<h2 class='h3 mb-3 font-weight-normal text-center'> Create New Client</h2>";
echo displayForm(
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
		"appended" => "
        <button class='btn btn-lg btn-primary btn-block' type='submit'>Create Client</button>
        "
	]
);

echo "<p class='h6 font-weight-bold text-center'>$error_message</p>";

require_once('includes/footer.php');