<?php
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

