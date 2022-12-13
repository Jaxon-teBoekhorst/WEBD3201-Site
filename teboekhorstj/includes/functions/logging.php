<?php
/**
 * This is for my WEBD-3201 course
 * This file contains logging functions to be used throughout the whole website
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(December, 1, 2022)
 */

require_once('./includes/functions.php');

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

/**
 * Log a password change success
 *
 * @param string $email email of the user changing their password
 * @return void
 */
function log_password_change(string $email)
{
	$file_name = "./logs/" . date("Y-m-d") . "_log.txt";
	$message = $email . " successfully changed their password at " . date("h:ia") . "\n";

	$file = fopen($file_name, "a");
	fwrite($file, $message);
	fclose($file);
}

/**
 * log a user requesting a password change
 *
 * @param string $email the email of the user requesting a password change
 * @param bool $success whether the user exists and an email is to be sent
 * @return void
 */
function log_reset_request(string $email, bool $success)
{
	$file_name = "./logs/" . date("Y-m-d") . "_log.txt";
	$message = sprintf(
		"%s has requested a password change at %s\n",
		$email,
		date("h:ia"));

	$file = fopen($file_name, "a");
	fwrite($file, $message);
	fclose($file);

	if ($success) {
		$file_name = "./logs/" . date("Y-m-d") . "_email_log.txt";

		try {
			$email = sprintf(
				"Hello, %s, your new password is %s, please use this to sign-in and change it immediately",
				$email,
				gen_rand_pass()
			);

			$file = fopen($file_name, "a");
			fwrite($file, $email);
			fclose($file);
		} catch (Exception $e) {
		}
	}
}