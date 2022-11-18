<?php
/**
 * This is for my WEBD-3201 course
 * This file contains functions to be used throughout the whole website
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(September, 13, 2022)
 */

/**
 * redirects the site to a new page and flush the output buffer
 *
 * @param string $url
 * @return void
 */
function redirect(string $url)
{
	header("Location:$url");
	ob_flush();
}

/**
 * Place the passed message in the $_SESSION array
 *
 * @param string $message message to use
 * @return void
 */
function set_message(string $message)
{
	$_SESSION['message'] = $message;
}

/**
 * Retrieve message from the $_SESSION array
 *
 * @return string
 */
function get_message(): string
{
	return $_SESSION['message'];
}

/**
 * check if there is a message in the $_SESSION array
 *
 * @return bool
 */
function is_message(): bool
{
	return isset($_SESSION['message']);
}

/**
 * clear the message from the $_SESSION array
 *
 * @return void
 */
function clear_message()
{
	unset($_SESSION['message']);
}

/**
 * gets the message and clears the value in the session
 *
 * @return string
 */
function flash_message(): string
{
	$message = is_message() ? get_message() : "";
	clear_message();
	return $message;
}

/**
 * check if any user is logged in
 *
 * @return bool
 */
function is_logged_in(): bool
{
	return !$_SESSION["current_user"] == "";
}

/**
 * sign a user in to the website
 * this will handle hashing before passing to SQL
 *
 * @param string $email
 * @param string $password
 * @return void
 */
function sign_in(string $email, string $password)
{
	$user = user_select($email);

	if (pg_num_rows($user) == 1) {

		$user_email = pg_fetch_result($user, 0, "EmailAddress");
		$user_pass = pg_fetch_result($user, 0, "Password");

		if ($email == $user_email && password_verify($password, $user_pass)) {
			$user_fname = pg_fetch_result($user, 0, "FirstName");
			$user_lname = pg_fetch_result($user, 0, "LastName");
			$user_type = pg_fetch_result($user, 0, "Type");
			$user_id = pg_fetch_result($user, 0, "Id");

			$_SESSION["current_user"] = $user_email;
			$_SESSION["user_fname"] = $user_fname;
			$_SESSION["user_lname"] = $user_lname;
			$_SESSION["user_type"] = $user_type;
			$_SESSION["user_id"] = $user_id;

			set_message("Welcome " . $user_fname . " " . $user_lname);
			update_accessed($user_id);
			log_sign_in($email, true);
			redirect("./dashboard.php");
		} else {
			set_message("Incorrect email or password");
			log_sign_in($email, false);
			redirect("sign-in.php");
		}
	} else {
		set_message("No user found");
		log_sign_in($email, false);
		redirect("sign-in.php");
	}
}

/**
 * log sign in attempts to log files that are stored with the date as the log files name
 *
 * @param string $email email of the user attempting to sign in
 * @param bool $success whether the user successfully signed in
 * @return void
 */
function log_sign_in(string $email, bool $success)
{
	$file_name = "./logs/" . date("Y-m-d") . "_log.txt";
	$message = $success ?
		"$email successfully signed in at " . date("h:ia") . '\n' :
		"$email failed to sign in at " . date("h:ia") . '\n';

	$file = fopen($file_name, "a");
	fwrite($file, $message);
	fclose($file);
}

/**
 * log sign-outs to log files that are stored with the date as the log files name
 *
 * @param string $email email of the user attempting to sign in
 * @return void
 */
function log_sign_out(string $email)
{
	$file_name = "./logs/" . date("Y-m-d") . "_log.txt";
	$message = $email . " successfully signed out at " . date("h:ia") . "\n";

	$file = fopen($file_name, "a");
	fwrite($file, $message);
	fclose($file);
}

function log_password_change(string $email)
{
	$file_name = "./logs/" . date("Y-m-d") . "_log.txt";
	$message = $email . " successfully changed their password at " . date("h:ia") . "\n";

	$file = fopen($file_name, "a");
	fwrite($file, $message);
	fclose($file);
}


/**
 * create a string of that contains the html to create a form
 *
 * Example: Display a form with a text input that stores its value to $_POST['text'],
 *            the default text is the $value variable,
 *            the label says Label Text,
 *            the css class is form-control,
 *            will autofocus and is a required value
 *
 * echo displayForm(
 *        [
 *            [
 *                "type" => "text",
 *                "name" => "text",
 *                "value" => $value,
 *                "label" => "Label Text",
 *                "class" => "form-control",
 *                "other" => "required autofocus"
 *            ],
 *        ]
 * );
 *
 * @param array $form an array containing all form elements and any other form components
 * @return string html string that contains the form
 */
function display_form(array $form): string
{
	$self = $_SERVER['PHP_SELF'];
	$formFinal = "\n<form action='$self' method='POST' class='form-signin align-content-center' enctype='multipart/form-data'>\n";
	if (isset($form['prepended'])) {
		$formFinal .= $form['prepended'];
	}

	foreach ($form as $element) {
		$type = $element['type'] ?? "";
		$name = $element['name'] ?? "";
		$value = $element['value'] ?? "";
		$label = $element['label'] ?? "";
		$class = $element['class'] ?? "";
		$other = $element['other'] ?? "";

		if ($type != "") {
			$formFinal .= "<input type=\"$type\" name=\"$name\" value=\"$value\" class=\"$class\" placeholder=\"$label\" $other/>\n";
		}
	}
	if (isset($form['appended'])) {
		$formFinal .= $form['appended'];
	}
	$formFinal .= "</form> \n";
	return $formFinal;
}

/**
 * This function take an array and will turn it into an html table
 *
 * @param array $table this contains the column information Format:
 *                        [[headers], <br/>
 *                           query result,  <br/>
 *                           amount of rows,  <br/>
 *                           selected page]
 * @return string html string that contains the table data
 */
function display_table(array $table): string
{
	// fetch values from array
	$header_array = $table[0];
	$rows = $table[1];
	$max_results = $table[2];
	$page = $table[3] - 1;

	/** String containing the final table */
	$result = "<table class='table'>\n";

	// generate headers
	/** array containing the table headers */
	$headers = [];
	foreach ($header_array as $header_key => $header_value) {
		$headers[] = $header_key;
		$result .= "<th>$header_value</th>";
	}

	/** Start point of the page loop */
	$start = RESULTS_PER_PAGE * $page - 1;
	/** End point of the page loop */
	$end = $start + RESULTS_PER_PAGE;

	if ($end >= $max_results) {
		// subtract one for array offset
		$end = $max_results - 1;
	}

	// loop through all rows
	for ($i = $start;
		 $i < $end;
		 $i++) {
		// populate row
		$result .= "<tr>\n";
		foreach ($headers as $header) {
			$row_data = pg_fetch_result($rows, $i + 1, $header);
			// display row data
			// if the header is an image type and row data isn't empty display it as an image
			if (in_array($header, IMAGE_HEADERS) && $row_data != '') {
				$result .= "<td><img class='' src='$row_data' alt='Client Logo' width='15%'></td>";
			} else {
				$result .= "<td>$row_data</td>";
			}
		}
		$result .= "\n</tr>\n";
	}

	// append table closer
	$result .= "</table>\n";
	// return final table
	return $result;
}

/**
 * function to check if specified page is the page that the user is on
 *
 * @param string $page specified page
 * @return string
 */
function is_active_page(string $page): string
{
	if ($_SERVER['PHP_SELF'] === "/webd3201/teboekhorstj/$page") {
		return 'active';
	} else {
		return '';
	}
}

/**
 * format phone numbers to the (111) 111-1111
 *
 * @param string $number input phone number
 * @return string
 */
function format_phone(string $number): string
{
	if (preg_match('/(\d{3})(\d{3})(\d{4})$/', $number, $matches)) {
		return "($matches[1]) $matches[2]-$matches[3]";
	} else {
		return $number;
	}
}
