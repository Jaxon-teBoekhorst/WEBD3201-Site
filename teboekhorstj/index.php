<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
 */

$title = "WEBD3201 Home Page";
$author = "Jaxon teBoekhorst";
$date = "13 September 2022";
$file = "./index.php";
$desc = "Homepage for my website";

require "./includes/header.php";
?>

<h1 class="cover-heading">Welcome!</h1>
<h2><?php echo flashMessage(); ?></h2>
<p class="lead">This is my homepage for my WEBD-3201 website</p>


<?php
require "./includes/footer.php";
?>