<?php
/**
 * This is for my WEBD-3201 course
 * This file contains database functions to be used throughout the whole website
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(September, 13, 2022)
 */


/**
 * generate a pg_connect with connection settings configured
 * @return false|resource
 */
function db_connect()
{
	$format = 'host=%1$s port=%2$s dbname=%3$s user = %4$s password=%5$s';
	return pg_connect(sprintf($format, DB_HOST, DB_PORT, DATABASE, DB_ADMIN, DB_PASSWORD));
}

$conn = db_connect();

pg_prepare($conn, "user_select", "SELECT * FROM users WHERE EmailAddress = $1");
pg_prepare($conn, "update_accessed", "UPDATE users SET LastAccess = $1 WHERE Id = $2");
pg_prepare($conn, "get_sales_people", "SELECT * FROM users WHERE Type = 's'");
pg_prepare($conn, "get_userId", "SELECT Id From users Where EmailAddress = $1");
pg_prepare($conn, "get_clients", "SELECT * FROM clients WHERE salesId = $1");
pg_prepare($conn, "check_for_client", "SELECT Id FROM clients WHERE email = $1");
pg_prepare($conn, "check_for_salesperson", "SELECT Id FROM users WHERE EmailAddress = $1");
pg_prepare($conn, "add_user", "INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, Enabled, Type) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)");
pg_prepare($conn, "add_client", "INSERT INTO clients(email, salesID, firstName, lastName, phoneNum, phoneExt, logopath) VALUES ($1, $2, $3, $4, $5, $6, $7)");
pg_prepare($conn, "get_calls_client", "SELECT * FROM calls WHERE client_id = $1");
pg_prepare($conn, "get_calls_salesperson", "SELECT c.email as email ,call_id as id, time FROM calls LEFT JOIN clients c on c.id = calls.client_id WHERE salesid = $1");
pg_prepare($conn, "add_call", "INSERT INTO calls(client_id, time) VALUES ($1, $2)");



/**
 * Return an object containing query results of the requested user
 * result contains all fields
 *
 * @param String $email of the user being retrieved
 * @return false|resource
 */
function user_select(string $email)
{
	$conn = db_connect();
	return pg_execute($conn, "user_select", [$email]);
}

/**
 * Executes an SQL query that updated the last time an account was accessed
 *
 * @param int $id id of the user being updated
 * @return void
 */
function update_accessed(int $id)
{
	$conn = db_connect();
	pg_execute($conn, "update_accessed", [date("Y-m-d H:i:s"), $id]);
}

/**
 * Returns a resource containing all salespeople in the database
 *
 * @return false|resource
 */
function get_sales_people()
{
	$conn = db_connect();
	return pg_execute($conn, "get_sales_people", []);
}

/**
 * return the user id from the database in the users table
 * that matches the email that is passed in
 *
 * @param string $email users email
 */
function get_userId(string $email)
{
	$conn = db_connect();
	return pg_execute($conn, "get_userId", [$email]);
}

/**
 * Returns a resource containing all client for all clients on a salesperson
 *
 * @param string $salesperson userId of the salesperson whose clients are being requested
 * @return false|resource
 */
function get_clients(string $salesperson)
{
	$conn = db_connect();
	return pg_execute($conn, "get_clients", [$salesperson]);
}

/**
 * checks if a salesperson has any clients
 *
 * @param string $email Salesperson email to check for clients
 * @return bool
 */
function check_for_client(string $email): bool
{
	$conn = db_connect();
	$client = pg_execute($conn, "check_for_client", [$email]);
	return pg_num_rows($client) == 1;
}

/**
 * check if a salesperson with the passed email is already in the database
 *
 * @param string $email email of the potential salesperson
 * @return bool
 */
function check_for_salesperson(string $email): bool
{
	$conn = db_connect();
	$salesperson = pg_execute($conn, "check_for_salesperson", [$email]);
	return pg_num_rows($salesperson) == 1;
}

/**
 * Updated the database adding a new user to the users table
 *
 * @param string $f_name users first name
 * @param string $l_name users last name
 * @param string $email users email
 * @param string $password user entered password
 * @return false|resource
 */
function add_salesperson(string $f_name, string $l_name, string $email, string $password)
{
	$password = password_hash($password, PASSWORD_BCRYPT);
	$conn = db_connect();
	return pg_execute($conn, "add_user", [$email, $password, $f_name, $l_name, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), true, 's']);
}

/**
 * add a client to the clients table
 *
 * @param string $f_name clients first name
 * @param string $l_name clients last name
 * @param string $email clients email
 * @param string $phone_num clients phone number
 * @param string $phone_ext clients phone number extension
 * @param int $sales_id id of the salesperson to tie this client to
 * @param string $logo_path the path pointing to the logo
 * @return false|resource
 */
function add_client(string $f_name, string $l_name, string $email, string $phone_num, string $phone_ext, int $sales_id, string $logo_path)
{
	$conn = db_connect();
	return pg_execute($conn, "add_client", [$email, $sales_id, $f_name, $l_name, $phone_num, $phone_ext, $logo_path]);
}

/**
 * retrieve all calls that the requested client has in the database
 *
 * @param int $client_id
 * @return false|resource
 */
function get_calls_client(int $client_id)
{
	$conn = db_connect();
	return pg_execute($conn, "get_calls_client", [$client_id]);
}

/**
 * retrieve all calls that the requested salesperson has in the database
 *
 * @param int $sales_id
 * @return false|resource
 */
function get_calls_salesperson(int $sales_id)
{
	$conn = db_connect();
	return pg_execute($conn, "get_calls_salesperson", [$sales_id]);
}

/**
 * add a call to the selected clients call records
 *
 * @param int $client_id the client to update
 * @return false|resource
 */
function add_call(int $client_id)
{
	$conn = db_connect();
	return pg_execute($conn, "add_call", [$client_id, date("Y-m-d H:i:s")]);
}