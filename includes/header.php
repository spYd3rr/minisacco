<?php
ob_start();
include_once 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        <?php
        if (isset($page_title))
        {
            echo 'Sharina - ' . $page_title;
        }
        else{
            echo "Sharina CMIS";
        }

        ?>
    </title>
    <base href="http://localhost/sherlo/"/>
    <link rel="stylesheet" href="assets/style2.min.css"/>
    <link rel="stylesheet" href="assets/js/google-code-prettify/prettify.css" />
    <link rel="stylesheet" href="assets/css/font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="assets/css/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="styles.css" />


    <link rel="shortcut icon" href="assets/ico/favicon.ico" type="image/x-icon" />
</head>
<body style="margin-top: 55px;">

<!-- start top-menu -->
<div class="top-menu navbar navbar-default navbar-fixed-top">
    <ul class="menu-list">
        <li><a href="index.php">Home</a></li>
        <li><a href="about-us.php">About Us</a> </li>
        <li><a href="contactUs.php">Contact Us</a> </li>
        <?php
        if(isset($_SESSION['loggedin'])){
            ?>
            <li><a href="members.php" class="btn">Members</a> </li>
            <li class="left-bar"></li>
            <li class="uname"><a href="dashboard.php" style="text-transform:capitalize;color: red" class="btn">Dashboard</a></li>
            <li class="left-bar"></li>
            <li class="uname" style="text-transform: capitalize">
                <?php
                if(isset($_SESSION['uname']))
                {
                    echo $_SESSION['uname'];
                }
                elseif(isset($_SESSION['admin']))
                {
                    echo 'Admin';
                }

                ?>
            </li>
            <li class="left-bar"></li>
            <li><i class="glyphicon glyphicon-log-out"></i>
                <a href="logout.php" style="text-transform:capitalize;color: red" title="Logout">Logout</a> </li>
            <?php
        }
        else{
            ?>
            <li><a href="reglog.php" class="btn">Account Options</a> </li>
            <?php
        }
        ?>

    </ul>
</div>


<!-- end top-menu -->
