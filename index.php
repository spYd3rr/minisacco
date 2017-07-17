<?php

$page_title  = "Home";
include_once "includes/header.php";
?>


<div class="body">
    <?php
    if (Sharina::getSessionState() == true)
    {
        ?>
        <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
    <?php
    }
    ?>
    <p class="intro">
        Welcome to Sherlo&reg; Chama. Here we will help you grow yourself monetary and we will also explore a whole lot of new ideas.<br/>
        We welcome new ideas in our movement and we hope that we will be of great use to you.
    </p>
    <p class="2">
        We have a whole section about ourselves on our <a href="about-us.php" class="inline-link">About Us</a> page
    </p>
</div>


<!-- footer -->
<?php
include_once "includes/footer.php";
?>
