<?php
/**
 * Created by PhpStorm.
 * User: kH1m
 * Date: 4/11/2017
 * Time: 10:50 AM
 */
$page_title = "Registration Receipt";
include_once '../includes/header.php';
if(Sharina::getSessionState() == False)
{
    header ("Location: ../index.php?action=notLoggedin");
}
?>

<!-- start body content -->
<div class="body">
    <div class="row">
        <!--registration-receipt-intro -->
        <div id="registration-receipt-intro-diaog" class="ui-dialog col-md-9" title="Select a email address" style="width:40%;position: relative;margin:0 0 0 30%;">
            <span class="ui-icon ui-icon-mail-closed" style="float:left; margin:0 7px 5% 0;"></span>
            Enter the email address you want to view
            <form class="form-inlin" method="POST" autocomplete="off">
                <input type="text" name="email-receipt" id="email-receipt-text" class="form-control" title="Enter email address"/><br/>
                <button type="submit" class="btn" id="show-registration-receipt" name="reg-receipt-email">OK</button>
            </form>
        </div>
        <!--end registration-receipt-intro dialog-->

        <?php
        /**
         * we only the sensitive data to users who are logged in only
         * so we check first if the user is logged in,
         * else show a message
         */

        // this code handles the registration_receipt

        if (isset($_POST['reg-receipt-email'])) {
            $userEmail = $_POST['email-receipt'];

            try {
                $getDataforReceipt = dbconnect()->prepare("SELECT * FROM users WHERE email=:email");
                $getDataforReceipt->execute(array(":email" => $userEmail));

                while ($resultSet = $getDataforReceipt->fetch(PDO::FETCH_ASSOC)) {
                    $regNo = $resultSet['ID'];
                    $fname = $resultSet['firstname'];
                    $lname = $resultSet['lastname'];
                    $phone = $resultSet['phone'];
                    $regDate = $resultSet['reg_date'];
                    $regFee = $resultSet['reg_fee'];

                }
                if (empty($userEmail)) {
                    $regreceipterror[] = "There was no email that was submitted!";
                }
                elseif ($getDataforReceipt->rowCount() == 0)
                {
                    $regreceipterror[] = "That email address does not exist on our system";
                }
            } catch (PDOException $e) {
                $regreceipterror = "An error occured while getting the user's email";
            }

            if (isset($_SESSION['uname'])) {
                // we show the normal uninterrupted view
                ?>

                <!-- The registration receipt -->
                <div id="registration-receipt" class="col-lg-8 ui-dialog" title="Registration Receipt" style="padding:14px 15px 0 15px;margin:1% 10% 2% 10%;width:80%;position: relative;">
                    <?php
                    if (isset($regreceipterror)) {
                        foreach ($regreceipterror as $error) {
                            ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php
                        }
                    }

                    if ($getDataforReceipt->rowCount() > 0)
                    {
                        ?>
                        <div class="form-group">
                            <div class="media-heading" style="font-weight: bolder;font-size: larger;color:red;text-decoration: underline">Registration Receipt</div>
                            <hr/>
                            <label for="registration number">Registration Number<br/>
                                <input type="text" disabled id="registration-number" class="" value="<?php if (!empty($regNo)) {
                                    echo $regNo;
                                } ?>"/>
                            </label>
                            <label for="registration number">First Name<br/>
                                <input type="text" disabled id="first-name" class="" value="<?php if (!empty($fname)) {
                                    echo ucfirst($fname);
                                } ?>"/>
                            </label>
                            <label for="registration number">Last Name<br/>
                                <input type="text" disabled id="last-name" class="" value="<?php if (!empty($lname)) {
                                    echo ucfirst($lname);
                                } ?>"/>
                            </label>
                        </div>
                        <div class="form-group thirty">
                            <label for="registration number">Phone Number</label>
                            <input type="text" disabled id="phone-number" class="form-control" value="<?php if (!empty($phone)) {
                                echo '+254' . $phone;
                            } ?>"/>
                        </div>
                        <div class="form-group thirty">
                            <label for="registration number">Email Address</label>
                            <input type="text" disabled id="phone-number" class="form-control" value="<?php if (!empty($userEmail)) {
                                echo $userEmail;
                            } ?>"/>
                        </div>
                        <div class="form-group thirty">
                            <label for="registration number">Date of Registration</label>
                            <input type="text" disabled id="registration-date" class="form-control" value="<?php if (!empty($regDate)) {
                                echo $regDate;
                            } ?>"/>
                        </div>
                        <div class="form-group thirty">
                            <label for="registration number">Registration Fee</label>
                            <input type="text" disabled id="registration-fee" class="" value="<?php if (!empty($regFee)) {
                                echo 'KSh:&nbsp;' . $regFee;
                            } ?>"/>
                        </div>
                        <div class="form-group float-right thirty">
                            <label for="todays-date"></label>
                            <input type="text" disabled id="todays-date" class="form-control" value="<?php echo TODAY; ?>"/>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <!-- /end the contribution receipt -->

                <?php
            }
            // else the user is not logged in and we tell them to login
            else{
                ?>
                <div id="not-logged-in" class="not-logged-in" title="Log in First" style="float: left;margin:0 0 0 30%;">
                    <p>
                        <?php echo NOT_LOGGED_IN_MESSAGE; ?>
                    </p>
                </div>
                <?php
            }
        }


        ?>

    </div>
</div>


<!-- Testing is being done here  -->





<!-- end body content -->
<!-- placed at the end of the pages so that pages load faster -->
<script src="assets/js/vendor/jquery-1.9.1.min.js"></script>
<script src="assets/js/vendor/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/vendor/bootstrap.js"></script>
<!--<script src="assets/js/vendor/holder.js"></script>-->
<script src="assets/js/vendor/jquery-ui-1.10.3.custom.min.js"></script>
<script src="assets/js/google-code-prettify/prettify.js"></script>
<script src="assets/js/docs.js"></script>
<!--<script src="assets/js/demo.js"></script>-->
<script type="application/javascript" src="sharina.js"></script>


</body>
</html>
