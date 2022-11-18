<?php
/**
 * This is for my WEBD-3201 course
 * This file contains the home page for my site
 *
 * This page has no functionality besides messages
 *
 * PHP Version 7.2
 *
 * @author Jaxon teBoekhorst
 * @version 1.0(September, 13, 2022)
 */

$title = "WEBD3201 Home Page";
$author = "Jaxon teBoekhorst";
$date = "13 September 2022";
$file = "./index.php";
$desc = "Homepage for my website";

require_once("./includes/header.php");
?>

<h1 class="cover-heading">Welcome!</h1>
<h2><?php echo flash_message(); ?></h2>
<p class="lead">This is my homepage for my WEBD-3201 website</p>


<?php
require "./includes/footer.php";
?>