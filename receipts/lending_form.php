<?php
/**
 * Created by PhpStorm.
 * User: kH1m
 * Date: 4/11/2017
 * Time: 10:50 AM
 */
$page_title  = "Lending Form";
include_once '../includes/header.php';


/**
 * we handle the user input now
 */
if (Sharina::getSessionState() == true)
{
    $username = $_SESSION['uname'];

    // we will use the session variable as our username to  get which user is making the contribution

    $memberNo   =   Sharina::getMemberNumber($username);
    $receiptNo  =   Sharina::getReceiptNumber('lending');
    if (isset($_POST['calculate']))
    {
        $amount         =   $_POST['amount'];
        $installment    =   $_POST['installment'];
        $reason         =   $_POST['reason'];

        if (empty($amount))
        {
            $lendreceipterror[]   = "You must fill in the amount you need";
        }
        elseif (empty($installment))
        {
            $lendreceipterror[]   = "You must provide how much installments you can afford";
        }
        elseif (AllowedInstallment($amount,$installment) == false)
            {
                $lendreceipterror[] = 'Your installment should not be less that 6.2% of the loan.';
            }

            //$duration  = DurationDue($amount,$installment);
            //$payable   = TotalPayable($amount,$installment);


    }
    if (isset($_POST['submit-request']))
    {
        $amount         =   $_POST['amount'];
        $installment    =   $_POST['installment'];
        $reason         =   $_POST['reason'];
        if (empty($amount))
        {
            $lendreceipterror[]   = "You must fill in the amount you need";
        }
        elseif (empty($installment))
        {
            $lendreceipterror[]   = "You must provide how much installments you can afford";
        }
        elseif (empty($reason))
        {
            $lendreceipterror[]   = "You must fill in the reason you need the loan";
        }
        else{
            Sharina::RequestLoan($username);
            $lendreceipterror[]  = "Success";
            redirect('../dashboard.php');
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

        // this code handles the registration_receipt



        if (isset($_SESSION['uname'])) {
            // we show the normal uninterrupted view
            ?>
            <!-- The registration receipt -->
            <div id="registration-receipt" class="col-lg-8 ui-dialog" title="Lending Form" style="margin:1% 20% 0 20%;width:60%;position: relative;">
                <?php
                if(Sharina::CheckPendingLoan($username) == true)
                {
                    ?>
                    <div class="alert alert-info" style="margin:3% 0 3% 0;"><i class="fa fa-mail"></i><?php echo PENDING_LOAN_BALANCE; ?>
                    <a href="receipts/RepayLoan.php" class="btn btn-primary">Repay Loan</a>
                    </div>
                    <?php
                }
                else{
                    if (isset($lendreceipterror)) {
                        foreach ($lendreceipterror as $error) {
                            ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php
                        }
                    }
                ?>
                <form method="post" class="form" style="width:50%;margin:0 25% 0 25%;" autocomplete="off">
                    <div class="bold">Request for a Loan</div>
                    <div class="form-group">
                        <label for="registration number">Member Number</label>
                        <input type="text" name="member-no" disabled  class="form-control" value="#<?php if (!empty($memberNo)) {
                            echo $memberNo;
                        } ?>" title="Member Number"/>

                        <label for="amount">Receipt Number</label>
                        <input type="text" disabled id="rec-no" name="receipt-name" class="form-control" value="#<?php if (!empty($receiptNo)) {
                            echo $receiptNo;
                        } ?>"/>

                        <label for="amount">Amount</label>
                        <input type="text" id="amount" name="amount" class="form-control" value="<?php if (!empty($amount)) {
                            echo $amount;
                        } ?>"/>

                        <label for="registration number">Monthly Installments</label>
                        <input type="text" id="installments" name="installment" class="form-control" value="<?php if (!empty($installment)) {
                            echo $installment;
                        } ?>"/>

                        <label for="registration number">Duration in Months</label>
                        <input type="text" disabled id="duration" name="duration" class="form-control" value="<?php if (!empty($amount) && !empty($installment)) {
                            echo DurationDue($amount,$installment); ;
                        } ?>"/>

                        <label for="todays-date">Interest per Month</label>
                        <input type="text" disabled id="disabled" name="interest" class="form-control" value="<?php echo SHARINA_INTEREST; ?>%"/>

                        <label for="registration number">Amount to be paid</label>
                        <input type="text" disabled id="payable" name="payable" class="form-control" value="<?php if (!empty($amount) && !empty($installment)) {
                            echo TotalPayable($amount,$installment);;
                        } ?>"/>

                        <label for="registration number">Why do you need the loan?</label>
                        <textarea id="reason" name="reason" class="form-control"><?php if (!empty($reason)) {
                                echo $reason;
                            } ?></textarea>

                        <label></label>
                        <div class="form-group">
                            <button type="submit" name="calculate" class="btn btn-primary" value="Calculate" title="Calculate Values">Calculate</button>
                            &nbsp;&nbsp;&nbsp;
                            <button type="submit" name="submit-request" class="btn btn-primary" value="Submit" title="Submit Loan Request">Submit</button>
                        </div>
                    </div>
                </form>
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
