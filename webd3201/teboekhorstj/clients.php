<?php
/*
 * Jaxon teBoekhorst
 * 04 October 2022
 * WEBD3201  
 */

$title = "WEBD3201 Calls Page";
$author = "Jaxon teBoekhorst";
$date = "04 October 2022";
$file = "./clients.php";
$desc = "display and create new clients for salespeople";

require_once("./includes/header.php");

$user = isset($_SESSION["current_user"]) ? isset($_SESSION["current_user"]) : "";
$user_type = $user != "" ? $_SESSION["user_type"] : '';

if (!($user_type == 'a' ||$user_type == 's')) {
    setMessage('Sorry, You do not have permission to use this page');
    redirect('./sign-in.php');
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
    $dropdown = "<select name='salesperson'>";
    foreach ($salespeople as $salesperson) {
        $dropdown .= "
        <br/>
        <option value='$salesperson'>$salesperson</option>
    ";
    }
    $dropdown .= "</select>";

    // drop down selection
    echo displayForm([
        "appended" => $dropdown
    ]);
}

// Create new clients
$client_f_name = $_POST['client_f_name'] ?? '';
$client_l_name = $_POST['client_l_name'] ?? '';
$client_email = $_POST['client_email'] ?? '';
$client_phone_num = $_POST['client_phone_num'] ?? '';
$client_phone_ext = $_POST['client_phone_ext'] ?? '';

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
            "type" => "text",
            "name" => "client_phone_num",
            "value" => $client_phone_num,
            "label" => "Client Phone Number",
            "class" => "form-control",
            "other" => "required"
        ],
        [
            "type" => "text",
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
