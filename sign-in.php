<?php
/*
 * Jaxon teBoekhorst
 * 13 September 2022
 * WEBD3201  
 */

$title = "WEBD3201 Login Page";
$author = "Jaxon teBoekhorst";
$date = "13 September 2022";
$file = "./sign-in.php";
$desc = "User sign in";
require "./includes/header.php";

if (isLoggedIn()) {
    redirect("./dashboard.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["inputEmail"]) && isset($_POST["inputPassword"])) {
        $email = $_POST["inputEmail"];
        $password = $_POST["inputPassword"];

        $_POST["inputEmail"] = "";
        $_POST["inputPassword"] = "";

        signIn($email, $password);
    }
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="form-signin">
    <h1 class="h3 mb-3 font-weight-normal text-center">Please sign in</h1>
    <h1 class="h4 mb-3 font-weight-normal text-center"><?php echo $message ?></h1>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" name="inputEmail" class="form-control" placeholder="Email address" required autofocus value="">
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" name="inputPassword" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>


<?php
require "./includes/footer.php";
