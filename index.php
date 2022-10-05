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

<h1 class="cover-heading">Cover your page.</h1>
<h2><?php echo flashMessage(); ?></h2>
<p class="lead">Cover is a one-page template for building simple and beautiful home pages. Download, edit the text, and add your own fullscreen background photo to make it your own.</p>


<?php
require "./includes/footer.php";
?>