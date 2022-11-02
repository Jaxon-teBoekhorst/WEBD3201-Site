<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
 */

function db_connect()
{
	return pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DATABASE . " user=" . DB_ADMIN . " password=" . DB_PASSWORD);
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
pg_prepare($conn, "add_client", "INSERT INTO clients(email, salesID, firstName, lastName, phoneNum, phoneExt) VALUES ($1, $2, $3, $4, $5, $6)");
pg_prepare($conn, "get_calls", "SELECT * FROM calls WHERE client_id = $1");
pg_prepare($conn, "add_call", "INSERT INTO calls(client_id, time) VALUES ($1, $2)");


function user_select($email)
{
	$conn = db_connect();
	return pg_execute($conn, "user_select", [$email]);
}

function update_accessed($id)
{
	$conn = db_connect();
	pg_execute($conn, "update_accessed", [date("Y-m-d H:i:s"), $id]);
}

function get_sales_people()
{
	$conn = db_connect();
	return pg_execute($conn, "get_sales_people", []);
}

function get_userId($email)
{
	$conn = db_connect();
	return pg_execute($conn, "get_userId", [$email]);
}

function get_clients($salesperson)
{
	$conn = db_connect();
	return pg_execute($conn, "get_clients", [$salesperson]);
}

function check_for_client($email): bool
{
	$conn = db_connect();
	$client = pg_execute($conn, "check_for_client", [$email]);
	return pg_num_rows($client) == 1;
}

function check_for_salesperson($email): bool
{
	$conn = db_connect();
	$salesperson = pg_execute($conn, "check_for_salesperson", [$email]);
	return pg_num_rows($salesperson) == 1;
}

function add_salesperson($f_name, $l_name, $email, $password)
{
	$password = password_hash($password, PASSWORD_BCRYPT);
	$conn = db_connect();
	return pg_execute($conn, "add_user", [$email, $password, $f_name, $l_name, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), true, 's']);
}


function add_client($f_name, $l_name, $email, $phone_num, $phone_ext, $sales_id)
{
	$conn = db_connect();
	return pg_execute($conn, "add_client", [$email, $sales_id, $f_name, $l_name, $phone_num, $phone_ext]);
}

function get_calls($client_id)
{
	$conn = db_connect();
	return pg_execute($conn, "get_calls", [$client_id]);
}

function add_call($client_id)
{
	$conn = db_connect();
	return pg_execute($conn, "add_call", [$client_id, date("Y-m-d H:i:s")]);
}