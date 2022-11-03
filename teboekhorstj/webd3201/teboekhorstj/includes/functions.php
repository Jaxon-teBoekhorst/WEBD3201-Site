<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
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
function setMessage(string $message)
{
	$_SESSION['message'] = $message;
}

/**
 * Retrieve message from the $_SESSION array
 *
 * @return string
 */
function getMessage(): string
{
	return $_SESSION['message'];
}

/**
 * check if there is a message in the $_SESSION array
 *
 * @return bool
 */
function isMessage(): bool
{
	return isset($_SESSION['message']);
}

/**
 * clear the message from the $_SESSION array
 *
 * @return void
 */
function clearMessage()
{
	unset($_SESSION['message']);
}

/**
 * gets the message and clears the value in the session
 *
 * @return string
 */
function flashMessage(): string
{
	$message = isMessage() ? getMessage() : "";
	clearMessage();
	return $message;
}

/**
 * check if any user is logged in
 *
 * @return bool
 */
function isLoggedIn(): bool
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
function signIn(string $email, string $password)
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

			setMessage("Welcome " . $user_fname . " " . $user_lname);
			update_accessed($user_id);
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

/**
 * log sign in attempts to log files that are stored with the date as the log files name
 *
 * @param string $email email of the user attempting to sign in
 * @param bool $success whether the user successfully signed in
 * @return void
 */
function logSignIn(string $email, bool $success)
{
	$file_name = "./logs/" . date("Ymd") . "_log.txt";
	$message = $success ?
		"Sign in success at " . date("h:ia") . ". User " . $email . "\n" :
		"Sign in failed at " . date("h:ia") . ". Email provided " . $email . "\n";

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
function logSignOut(string $email)
{
	$file_name = "./logs/" . date("Ymd") . "_log.txt";
	$message = $email . "successfully signed out at " . date("h:ia") . "\n";

	$file = fopen($file_name, "a");
	fwrite($file, $message);
	fclose($file);
}


/**
 * create a string of that contains the html to create a form
 *
 * Example: Display a form with a text input that stores its value to $_POST['text'],
 * 			the default text is the $value variable,
 * 			the label says Label Text,
 * 			the css class is form-control,
 * 			will autofocus and is a required value
 *
 * echo displayForm(
 * 		[
 *			[
 *				"type" => "text",
 *				"name" => "text",
 *				"value" => $value,
 *				"label" => "Label Text",
 *				"class" => "form-control",
 * 				"other" => "required autofocus"
 *			],
 * 		]
 * );
 *
 * @param array $form an array containing all form elements and any other form components
 * @return string
 */
function displayForm(array $form): string
{
	$self = $_SERVER['PHP_SELF'];
	$formFinal = "<form action='$self' method='POST' class='form-signin align-content-center'>";
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
 * function to check if specified page is the page that the user is on
 *
 * @param string $page specified page
 * @return string
 */
function isActivePage(string $page): string
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
function formatPhone(string $number): string
{
	if (preg_match('/(\d{3})(\d{3})(\d{4})$/', $number, $matches)) {
		return "($matches[1]) $matches[2]-$matches[3]";
	} else {
		return $number;
	}
}
