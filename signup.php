<?php
$page_title = "Create Account";
// we call the 'database.php' file which has our database variables
include_once "includes/database.php";
if(isset($_SESSION['loggedin']))
{
    redirect("index.php");
}

//validating the from inputs

    if ($_SERVER['REQUEST_METHOD'] == "POST")
        // this only returns true if the form method is "POST"
    {
        // assign variable to our from inputs
        $firstname          = $_POST['fname'];
        $lastname           = $_POST['lname'];
        $username           = $_POST['uname'];
        $email              = $_POST['email'];
        $nationalID         = $_POST['nationalID'];
        $password           = $_POST['pword'];
        $phone              = $_POST['phone'];
        $current_dateTime   =   date('D, M m Y @ H:i:s');

        if(empty($firstname)){
            $error[] = "You must provide your firstname";
        }
        elseif (!preg_match("/^[a-zA-Z ]*$/",$firstname)) {
            $error[] = "Only letters and white space allowed for the firstname";
        }
        elseif(empty($lastname)){
            $error[] = "You must provide your lastname";
        }
        elseif (!preg_match("/^[a-zA-Z ]*$/",$lastname)) {
            $error[] = "Only letters and white space allowed for the lastname";
        }
        elseif(empty($username)){
            $error[] = "You must provide your username";
        }
        elseif (!preg_match("/^[a-zA-Z]*$/",$username)) {
            $error[] = "Only letters and no white spaces allowed for the username!";
        }
        elseif(empty($email)){
            $error[] = "You must provide your email";
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = "Invalid email format!";
        }
        elseif(empty($nationalID)){
            $error[] = "You must provide your national ID number";
        }
        elseif(strlen($nationalID) > 8){
            $error[] = "The ID number should be 8 digits!";
        }
        elseif(strlen($nationalID) < 8){
            $error[] = "The ID number should be 8 digits!";
        }
        elseif(empty($phone)){
            $error[] = "You must provide a phone number";
        }
        elseif(strlen($phone) > 10){
            $error[] = "The phone number should be 10 digits!";
         }
        elseif(strlen($phone) < 10){
            $error[] = "The phone number should be 10 digits!";
        }
        elseif(empty($password)){
            $error[] = "You must provide a password";
        }
        else{
            // all the data verified sucessfully, now we save it to the database



            // we check if the details already exist in the database to prevent duplicates
            $checkAvailability  =   dbconnect()->prepare("SELECT * FROM users WHERE username=:username OR email=:email OR phone=:phone");
            $checkAvailability->execute(array(
                ":username" =>  $username,
                "email"     =>  $email,
                ":phone"    => $phone,
            ));
            while ($results = $checkAvailability->fetch(PDO::FETCH_ASSOC))
            {
                if ($username = $results['username']){
                    $error[] = "That username is already registered. Chose another!";
                }
                elseif ($email = $results['email']){
                    $error[] = "That email address is already registered with us.";
                }
                elseif($phone = $results['phone']){
                    $error[]  = "That phone number is already registered with us";
                }
                elseif($nationalID = $results['nationalID']){
                    $error[]  = "That ID number is already registered with us";
                }
            }

            //read more on prepared statements used below
            // we call the Bcrypt class in Bcrypt.php to encrypt our password
            $password_hash  = Bcrypt::hashPassword($password);
            try{

                $reg = $connection->prepare("INSERT INTO `users` (`firstname`,`lastname`,`username`,`userLevel`,`email`,`nationalID`,`password`,`phone`,`reg_date`,`reg_fee`) 
                                            VALUES(:firstname,:lastname,:username,:userLevel,:email,:nationalID,:password,:phone,:reg_date,:reg_fee)");
                // dbconnect()->bindParam(":firstname", $firstname);

                $reg->execute(array(
                    ":firstname"    => $firstname,
                    ":lastname"      => $lastname,
                    ":username"     => $username,
                    ":userLevel"     => 'user',
                    ":email"        => $email,
                    ":nationalID"   => $nationalID,
                    // we save the encrypted password
                    ":password"     => $password_hash,
                    ":phone"        => $phone,
                    ":reg_date"     => $current_dateTime,
                    ":reg_fee"      => '200'
                ));
                $lastInsertId = $connection->lastInsertId('ID');
                $recordinmoneypool = $connection->prepare("INSERT INTO moneypool (user_ID,totalamount) VALUES (:user_ID,:initialamount)");
                $recordinmoneypool->execute(array(
                    ":user_ID"  =>  $lastInsertId,
                    ":initialamount" => 0.00
                ));
                $success = "You have successfully registered. You can log in <a href='login.php' class='btn btn-primary'>here</a>";
            }
            catch(PDOException $e){
                echo "OOPSIE!!" . $e->getMessage();
            }

        }
    }

// then we call the page header

include_once 'includes/header.php';
?>

<!-- start body content -->
<div class="body">

    <!-- start signup form -->
    <div class="form ui-dialog" style="position: relative;width: 40%;background-color: #dde491;opacity: 43;">
        <?php
            if(isset($error))
            {
                foreach($error as $error)
                {?>
                    <div class="alert alert-danger text-center">
                        <i class="ui-icon ui-icon-alert"></i><?php echo $error . '!'; ?>
                    </div>
        <?php
                }
            } elseif(isset($success)){
                ?>
                <div class="alert alert-success text-center">
                    <?php echo $success . '!'; ?>
                </div>
                <?php
            }
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
            <div class="form-group">
                <label for="firstname"></label>
                <input type="text" name="fname" class="form-control" placeholder="firstname" value="<?php if(isset($error)) {echo $firstname;} ?>">
            </div>
            <div class="form-group">
                <label for="lastname"></label>
                <input type="text" name="lname" class="form-control" placeholder="lastname" value="<?php if(isset($error)) {echo $lastname;} ?>">
            </div>
            <div class="form-group">
                <label for="username"></label>
                <input type="text" name="uname" class="form-control" placeholder="username" value="<?php if(isset($error)) {echo $username;} ?>">
            </div>
            <div class="form-group">
                <label for="email"></label>
                <input type="text" name="email" class="form-control" placeholder="email" value="<?php if(isset($error)) {echo $email;} ?>">
            </div>
            <div class="form-group">
                <label for="nationalID"></label>
                <input type="text" name="nationalID" class="form-control" placeholder="national ID number" value="<?php if(isset($error)) {echo $nationalID;} ?>">
            </div>
            <div class="form-group">
                <label for="phone"></label>
                <input type="tel" name="phone" class="form-control" placeholder="phone number" value="<?php if(isset($error)) {echo $phone;} ?>">
            </div>
            <div class="form-group">
                <label for="password"></label>
                <input type="password" name="pword" class="form-control" placeholder="password">
            </div>
            <div>
                <button type="submit" name="signup" class="btn btn-success">SIGN ME UP!</button>
            </div>

        </form>
    </div>
    <!-- end signup form -->

</div>
<!-- end body content -->

</body>
</html>