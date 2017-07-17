<?php
/**
 * Created by PhpStorm.
 * User: kH1m
 * Date: 5/7/2017
 * Time: 7:03 PM
 */

$page_title = "Withdrawal Form";

include_once '../includes/header.php';

/**
 * we handle the user input now
 */
if (Sharina::getSessionState() == true)
{
    $username = $_SESSION['uname'];
    // we will use the session variable as our username to  get which user is making the contribution

    $memberNo       = Sharina::getMemberNumber($username);
    $memberNames    = Sharina::getMemberNames($username);
    $receiptNo      = Sharina::getReceiptNumber('withdrawals');
    $TransactionCode= GenerateTransactionCode(10);

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // this means that the user is logged in
        $amount         = $_POST['amount'];
        $reason         = $_POST['reason'];
        if (empty($amount))
        {
            $error[]  = "You have not placed any amount yet!";
        }
        elseif (empty($reason))
        {
            $error[]  = "Why do you need to withdraw the money please?";
        }
        elseif ($amount > Sharina::GetUsersTotalMoney($username))
        {
            $error[]  = "That amount cannot be afforded right now. Reduce it.";
        }
        else{
            Sharina::MakeWithdraw($username,$TransactionCode);
            $success[] = "You have successfully made a withdrawal. <br/>Your transaction code is <br/><green>" . $TransactionCode .
                "</green><br/><br/> <a href='dashboard.php' class='btn btn-warning'>OK</a>";

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
            <div id="contribution-form" class="col-lg-1 ui-dialog" title="Withdrawal Form" style="width:50%;margin:1% 25% 2% 25%;position: relative;">
                <?php
                // we will handle some stuff here
                if (isset($success))
                {
                    foreach ($success as $success) {
                        ?>
                        <div class="alert alert-success" id="success-contribution" title="Withdrawal Form"><?php echo $success ?></div>
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

                        <label for="amount">Receipt Number</label>
                        <input type="text" disabled id="rec-no" name="receipt-name" class="form-control" value="#<?php if (!empty($receiptNo)) {
                            echo $receiptNo;
                        } ?>"/>

                        <label for="amount">Amount to Withdraw</label>
                        <input type="text" id="amount" name="amount" class="form-control" value="<?php if (!empty($amount)) {
                            echo $amount;
                        } ?>"/>

                        <label for="todays-date">Reason for Withdraw</label>
                        <textarea type="text" id="reason" name="reason" class="form-control"><?php if (!empty($reason)) {
                            echo $reason;
                        } ?></textarea>



                        <label></label>
                        <button type="submit" name="withdraw" class="btn btn-primary form-control" value="withdraw">Withdraw</button>
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
<?php
include_once "../includes/footer.php";