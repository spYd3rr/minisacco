<?php
// call our 'database.php' file which contains our database variables
include_once "includes/database.php";

$page_title = "Login";

if(isset($_SESSION['loggedin']))
{
    redirect("index.php");
}

//validate the form
if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $username      = $_POST['uname'];
    $password       = $_POST['pword'];

    if(empty($username))
    {
        $error[] = "Provide username or email";
    } elseif(empty($password))
    {
        $error[] = "Provide password";
    }
    else{
        
try {
    // check if the data the user provided is in the database
    $check = dbconnect()->prepare("SELECT * FROM users WHERE username=:username OR email=:email");
    $check->execute(array(
        ":username" => $username,
        ":email" => $username,
    ));
    if($check->rowCount() == 0)
    {
        $error[]  = "Invalid credentials!";
    }

    while($results = $check->fetch(PDO::FETCH_ASSOC))
    {
        $passwordfromdb = $results['password'];
        $usernamefromdb = $results['username'];
        // if no data is returned, we show an error
        if (Bcrypt::checkPassword($password,$passwordfromdb) != true) {
            $error[] = "Invalid credentials!";
        } else{
            // user is admin
            if($results['userLevel']  == 'admin')
            {
             $_SESSION['admin']   =  'Admin';
            }
            else{
                // the user exists in the database
                // so we create a session for them
                $_SESSION['uname'] = $usernamefromdb;
            }

            $_SESSION['loggedin'] = True;
            redirect("dashboard.php");
        }
    }

}
catch(PDOException $e){
    echo $e->getMessage();
}
    }
}

// call the page header
include_once 'includes/header.php';
?>

<!-- start body content -->
<div class="body">

    <!-- begin log in form -->
    <div class="form">
        <?php
        if (isset($error))
        {
            foreach($error as $error)
            {
                ?>
                <div class="alert-danger text-center"><?php echo $error;?></div>
                <?php
            }
        }
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
            <div class="form-group">
                <label for="uname/email"></label>
                <input type="text" name="uname" class="form-control" placeholder="username / email">
            </div>
            <div class="form-group">
                <label for="uname/email"></label>
                <input type="password" name="pword" class="form-control" placeholder="password">
            </div>
            <div>
                <button type="submit" name="signup" class="btn btn-success">LOG ME IN!</button>
            </div>

        </form>
    </div>
    <!-- end login form -->

</div>
<!-- end body content -->

</body>
</html>
