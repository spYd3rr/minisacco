<?php
/**
 * Created by PhpStorm.
 * User: kH1m
 * Date: 4/25/2017
 * Time: 6:54 PM
 */


$page_title = "Repay Loan";
include_once '../includes/header.php';

/**
 * we handle the user input now
 */


if (Sharina::getSessionState() == true)
{
    $username = $_SESSION['uname'];
    // we first check if the user has a pending loan
    if(Sharina::CheckPendingLoan($username) == False)
    {
        redirect('../dashboard.php');
    }
    elseif (Sharina::GetUnpaidAmount($username) < 1){
        //redirect('../dashboard.php');
        // the user ha cleared their balance
        $updatepaidState   =   dbconnect()->prepare("UPDATE lending set paid=:paidState,remaining=:remaining WHERE ID=:loanID");
        $updatepaidState->execute(array(
            ":paidState"    => 1,
            ":remaining"    => 0,
            ":loanID"      => Sharina::GetUnpaidLoanID($username),
        ));
        redirect('../dashboard.php');

    }

    // we will use the session variable as our username to  get which user is making the contribution

    $memberNo       = Sharina::getMemberNumber($username);
    $memberNames    = Sharina::getMemberNames($username);
    $UnpaidAmount   = Sharina::GetUnpaidAmount($username);
    $PreferredInstallment = Sharina::GetPreferredInstallment($username);

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // this means that the user is logged in
        $amount         = $_POST['amount'];
        if (empty($amount))
        {
            $error[]  = "You have not placed any amount yet!";
        }
        elseif ($UnpaidAmount < $amount)
        {
            $error[] = "The value you have entered cannot be greater than the remaining amount!";
        }
        elseif($amount < $PreferredInstallment && $UnpaidAmount > $PreferredInstallment)
        {
            // the amount the user puts should not be greater than the pending balance
            // and also if the ending balance is less than the installments they specified,
            // the user can directly input the balance and et it over with
            $error[]  = "The amount can not be less than the installment you specified during lending!";
        }
        else{
                Sharina::RepayLoan($username);
                redirect('RepayLoan.php');

        }



    }
}
?>

<!-- start body content -->
<div class="body">
    <div class="row">
        <?php
        /**
         * we only the sensitive data to users who are logged in only
         * so we check first if the user is logged in,
         * else show a message
         */

        if (isset($_SESSION['uname'])) {
            // we show the normal uninterrupted view
            ?>
            <!-- The registration receipt -->
            <div class="col-lg-1 ui-dialog" title="Repay Loan" style="width:50%;margin:1% 25% 0 25%;position: relative;">
                <?php
                // we will handle some stuff here
                if (isset($success))
                {
                    foreach ($success as $succes) {
                        ?>
                        <div class="alert alert-success"><?php echo $succes ?></div>
                        <?php
                    }
                }
                if(isset($error))
                {
                    foreach ($error as $err)
                    {
                        ?>
                        <div class="alert alert-warning"><?php echo $err; ?></div>
                        <?php
                    }
                }

                ?>
                <form method="post" class="form" style="" autocomplete="off">
                    <div class="form-group">
                        <label for="registration number">Member Number</label>
                        <input type="text" name="member-no" disabled id="registration-number" class="form-control" value="#<?php if (!empty($memberNo)) {
                            echo $memberNo;
                        } ?>"/>

                        <label for="registration number">Member Names</label>
                        <input type="text" disabled id="full-name" name="member-name" class="form-control" value="<?php if (!empty($memberNames)) {
                            echo $memberNames;
                        } ?>"/>

                        <label for="todays-date">Date Awarded</label>
                        <input type="text" disabled id="date" name="date" class="form-control" value="<?php echo Sharina::GetLoanedDate($username); ?>"/>

                        <label for="amount">Unpaid Amount</label>
                        <input type="text" disabled id="unpaid" name="unpaid" class="form-control" value="<?php echo Sharina::GetUnpaidAmount($username); ?>"/>

                        <label for="amount">Amount you are paying</label>
                        <input type="text" id="amount" name="amount" class="form-control"/>
                        
                        <label></label>
                        <button type="submit" name="pay" class="btn btn-primary form-control" value="pay">Pay</button>
                    </div>
                </form>

            </div>
            <!-- /end the contribution receipt -->

            <?php

        }
// else the user is not logged in and we tell them to login
        else{
            ?>
            <div id="not-logged-in" class="not-logged-in" title="Log in First">
                <p>
                    <?php echo NOT_LOGGED_IN_MESSAGE; ?>
                </p>
            </div>
            <?php
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
