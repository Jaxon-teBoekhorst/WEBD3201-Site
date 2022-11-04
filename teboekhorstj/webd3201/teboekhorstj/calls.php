<?php
/**
 * This is for my WEBD-3201 course
 * This file contains the calls page for my website
 *
 * This page allow salespeople to add call records to their clients and view what calls they have made with their clients
 * Admins are able to do everything that salespeople can, though they can select which salesperson it applies to
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(October, 04, 2022)
 */

$title = "WEBD3201 Calls Page";
$author = "Jaxon teBoekhorst";
$date = "04 October 2022";
$file = "./calls.php";
$desc = "View and add new calls";

require_once("./includes/header.php");

$user = $_SESSION["current_user"] ?? "";
$user_type = $user != "" ? $_SESSION["user_type"] : "";
$user_id = $user != "" ? $_SESSION["user_id"] : "";
$selected_client = $_POST['current_client'] ?? '';

if (!($user_type == 'a' || $user_type == 's')) {
	set_message('Sorry, You do not have permission to use this page');
	redirect('./sign-in.php');
}

echo "<h1 class='h4 mb-3 font-weight-normal text-center'>$message</h1>";

if ($user_type == 'a') {
	// get selected user and their id
	$current_salesperson = $_SESSION['selected_salesperson'] ?? 'jax.tebs+webd3201salesperson@outlook.com';
	$current_user_id = get_userId($current_salesperson);
	$current_user_id = pg_fetch_result($current_user_id, '0', 'Id');
} else {
	$current_salesperson = $user;
	$current_user_id = $user_id;
}

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

		// add call to database
		if (!add_call($selected_client)) {
			set_message("Failed to add a call for $selected_client");
			redirect("calls.php");
		}else{
			set_message("Success!");
			redirect("calls.php");
		}
	}
}

// check if the user is an admin
if ($user_type == 'a') {
	// get a list of salespeople emails
	$result = get_sales_people();
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
			"<button class='btn btn-lg btn-primary btn-block mt-3 mb-5' name='btnSalesperson' type='submit'>Show Calls</button>"
	]);
}

$result = get_clients($current_user_id);
$clients = [];
for ($i = 0; $i < pg_num_rows($result); $i++) {
	$client = pg_fetch_row($result, $i);
	$clients[] = ["$client[3] $client[4] ($client[1])", $client[0]];
}

if (sizeof($clients) > 0) {
	// generate dropdown selections
	$dropdown = "<select class='form-control mb-auto' style='width: auto; height: auto' name='current_client'>";

	foreach ($clients as $client) {
		if ($selected_client == $client) {
			$dropdown .= "<option value='$client[1]' selected='selected'>$client[0]</option>";
		} else {
			$dropdown .= "<option value='$client[1]'>$client[0]</option>";
		}


	}
	$dropdown .= "</select>";

	echo display_form([
		"appended" => $dropdown .
			"<button class='btn btn-lg btn-primary btn-block mt-3 mb-5' type='submit'>Add Call</button>"
	]);
} else {
	if ($user_type == 'a') {
		echo "<p class='h4 text-center mb-5'>The selected salesperson does not have any clients</p>";
	} else {
		echo "<p class='h4 text-center mb-5'>You have no clients</p>";
	}
}

foreach ($clients as $client) {
	echo "<p class='h5'>$client[0]</p>";
	echo "<ul>";
	$calls = get_calls($client[1]);
	if (pg_num_rows($calls) == 0) {
		echo "This client has no calls on record";
	} else {
		for ($i = 0; $i < pg_num_rows($calls); $i++) {
			$call = pg_fetch_row($calls, $i);
			echo "<li>$call[0] - $call[2]</li>";
		}
	}
	echo "</ul>";
}

require_once('includes/footer.php');