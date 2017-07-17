<?php


$page_title = "Choose Account Option";
include_once "includes/header.php";
include_once 'includes/database.php';
if(isset($_SESSION['loggedin']))
{
    redirect("index.php");
}
?>
<!-- end top-menu -->

<!-- start body content -->
<div class="body">
<p class="choose">
    If you do not have an account, click the Signup button below to create an account.<br/>
    <a href="signup.php"><button class="btn btn-danger">Signup</button></a> <br/><br/>
    Do you have an account with us? If yes, please click the button below to log in.<br/>
    <a href="login.php"><button class="btn btn-primary">Login</button></a> <br/><br/>
</p>
</div>
<!-- end body content -->
<?php
include_once "includes/footer.php";
