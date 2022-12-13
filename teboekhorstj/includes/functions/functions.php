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

require_once('./includes/functions/logging.php');
require_once('./includes/functions/messages.php');

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
        $enabled = pg_fetch_result($user, 0, "enabled");

        if (!$enabled) {
            set_message("Sorry you can't currently sign-in, please talk to HR");
            redirect("./index.php");
        }

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
    for ($i = $start; $i < $end; $i++) {
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

/**
 * Generate a cryptographically secure random password
 *
 * @param int $length
 * @return string the generated password
 * @throws RangeException thrown when the passed length is too small
 * @throws Exception thrown by random_int function
 */
function gen_rand_pass(int $length = 12): string
{
    // basic charsets
    define("chars", 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    define("spec_chars", "!@#$%^&*()_+,.<>;`~");
    define("digits", "1234567890");

    // merge all basic charsets lists with reg chars upper and lower case
    $char_set = chars . strtolower(chars) . spec_chars . digits;

    if ($length < 1) {
        throw new RangeException("Length must be an integer greater than 0");
    }


    // array of the characters to return
    $password = [];
    // find the length of the charset and offset to 0 start
    $max = strlen($char_set) - 1;


    for ($i = 0; $i < $length; $i++) {
        // append random char to password
        $password [] = $char_set[random_int(0, $max)];
    }

    // return the final password
    return implode('', $password);
}

/**
 * Print a value then stop processing the page
 *
 * @param $value _ the value to be dumped (can be anything)
 * @return void
 */
function _dd($value) {
    var_dump($value);
    die();
}