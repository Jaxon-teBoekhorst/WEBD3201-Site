<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
 */

ob_start();
if (session_id() == "") {
    session_start();
}
if (!isset($_SESSION["current_user"])) {
    $_SESSION["current_user"] = "";
}

$user = isset($_SESSION["current_user"]) ? isset($_SESSION["current_user"]) : "";
if ($user != '') {
    $user_type = isset($_SESSION["user_type"]) != "" ? $_SESSION["user_type"] : "";
}

require_once("./includes/constants.php");
require_once("./includes/db.php");
require_once("./includes/functions.php");
?>

<!-- <?php echo $author ?> -->
<!-- <?php echo $date ?> -->
<!-- <?php echo $file ?> -->
<!-- <?php echo $desc ?> -->

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>
        <?php echo $title; ?>
    </title>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./css/styles.css" rel="stylesheet">

    <?php
    $message = flashMessage();
    ?>

</head>

<body>
<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="./index.php">Jaxon teBoekhorst</a>
    <ul class="navbar-nav px-3">
        <?php if (!isLoggedIn()) { ?>
            <li class="nav-item text-nowrap">
                <a class="nav-link " href="./sign-in.php">Sign In</a>
            </li>
        <?php } else if (isLoggedIn()) { ?>
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="./logout.php">Sign Out</a>
            </li>
        <?php } ?>
    </ul>
</nav>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <?php if (isLoggedIn()) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isActivePage('dashboard.php') ?>"
                               href="./dashboard.php">
                                Dashboard
                            </a>
                        </li>
                        <?php
                        if ($user_type == 'a') : ?>
                            <li class="nav-item ">
                                <a class="nav-link <?php echo isActivePage('salespeople.php') ?>"
                                   href="./salespeople.php">
                                    Sales People
                                </a>

                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isActivePage('clients.php') ?>"
                               href="./clients.php">
                                Clients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isActivePage('calls.php') ?>"
                               href="./calls.php">
                                Calls
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
            <div>