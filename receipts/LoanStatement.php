<?php
/**
 * Created by PhpStorm.
 * User: kH1m
 * Date: 4/25/2017
 * Time: 10:26 AM
 */

$page_title = "Loan Statement";
include_once '../includes/header.php';

/**
 * we only the sensitive data to users who are logged in only
 * so we check first if the user is logged in,
 * else show a message
 */

// this code handles the contribution_receipt
?>
<!-- start body content -->
    <div class="body">
        <div class="row">
<?php
if (isset($_SESSION['uname'])) {
    $username = $_SESSION['uname'];

    // first we get the users details from the database
    $RegDate    =   Sharina::getRegistrationDate($username);
    $explodedDate = explode('@', $RegDate);
    $Date  = $explodedDate[0];

    $memberNo   =   Sharina::getMemberNumber($username);
    $selectAllLendings =   dbconnect()->prepare("SELECT * FROM lending WHERE user_ID=:memberNo AND paid=:paidState");
    $selectAllLendings->execute(array(":memberNo"=>$memberNo, ":paidState"=>0));
    $rows   = $selectAllLendings->fetchAll();
    ?>


            <!-- The loan statement receipt -->
            <div id="registration-receipt" class="col-lg-12 ui-dialog" title="Loan Statement" style="width:80%;margin:1% 10% 5% 10%;position: relative;">
                <div class="media-heading" style="font-weight: bolder;font-size: larger;color:red;text-decoration: underline">Loan Statement</div>
                <hr/>
                <div>
                    <div class="form-inline">
                        Full Names:
                        <input  type="text" disabled value="<?php echo Sharina::getMemberNames($username); ?>" class="for-control" />

                        <span>Registration Date:</span>
                        <input type="text" disabled value="<?php echo $Date; ?>" class="formcontrol" />
                    </div>
                    <hr/>
                    <?php
                    // if user has no contributions yet, show a message
                    if ($selectAllLendings->rowCount() == 0){
                        ?>
                        <div class="alert alert-info"><i class="ui-icon ui-icon-alert"></i><?php echo NO_LOAN_HISTORY; ?> </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="col-md-6">
                            <table class="table-hover table-bordered" style="margin:0 0 15px 0;">
                                <?php
                                foreach ($rows as $columnArr)
                                {
                                    $amount = $columnArr['amount_requested'];
                                    $duration = $columnArr['duration'];
                                    $installment = $columnArr['installment'];
                                    $payable = $columnArr['payable'];
                                    ?>
                                    <tr>
                                        <td colspan="2" class="danger bolder">Amount Requested</td>
                                        <td>KSh:&nbsp;<?php echo $amount; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="danger bolder">Duration for Payment</td>
                                        <td><?php echo $duration; ?>&nbsp;Months</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="danger bolder">Amount Payable</td>
                                        <td>Ksh:&nbsp;<?php echo $payable; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="danger bolder">Monthly Installment</td>
                                        <td>Ksh:&nbsp;<?php echo $installment; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="danger bolder">Date Requested</td>
                                        <td><?php echo $columnArr['date']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="danger bolder">Reason Given</td>
                                        <td><?php echo $columnArr['reason'] ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                            <?php
                            // we get the payment history and show it here if the user has not yet completed paying
                            try{
                                $stmt = dbconnect()->prepare("SELECT * FROM repays WHERE user_ID=:user_ID AND loanID=:loanID");
                                $stmt->execute(array(
                                    ":user_ID"  => Sharina::getMemberNumber($username),
                                    ":loanID"   => Sharina::GetUnpaidLoanID($username),
                                ));
                                $resultSet = $stmt->fetchAll();


                            } catch(PDOException $e)
                            {
                                echo "An error is preventing your full loan history from being shown.";
                            }

                            // if the user has a repay history, we can show it to them
                            if ($stmt->rowCount() > 0)
                            {
                                ?>
                                <div style="width:140%;line-height: 60px;margin:0 0 10px 0;">
                                <hr/>
                                <blue>You have currently paid your loan as shown below</blue><br/>
                                <table class="table-hover table-bordered">
                                    <tr>
                                        <td colspan="2" class="success">Date</td>
                                        <td colspan="2" class="success">Amount Paid</td>
                                        <td colspan="2" class="success">Amount Remaining</td>
                                    </tr>

                                    <?php
                                    foreach ($resultSet as $item) {
                                        $FullDateTime = $item['date'];
                                        $explodedDateTime = explode(':',$FullDateTime);
                                        $date = $explodedDateTime[0];
                                        $time = $explodedDateTime[1];
                                        ?>
                                        <tr>
                                            <td colspan="2"><?php echo $date; ?></td>
                                            <td colspan="2">KSh. <?php echo $item['amount']; ?></td>
                                            <td colspan="2">KSh. <?php echo $item['remaining']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="col-md-6">
                            <hr/>
                            Interest on loan:<?php echo SHARINA_INTEREST . '%';?><hr/>
                            You borrowed <green>KSh: <?php echo $amount;?></green>. <br/>This amount should be paid in <green><?php echo $duration; ?> months</green>.<br/>
                            Your monthly installments are KSh:<green><?php echo $installment;?></green>.<br/>
                            The total amount payable is <green>KSh:<?php echo $payable;?></green> considering our constant interest of <red><?php echo SHARINA_INTEREST; ?>% per month</red>.

                        </div>

                        <?php
                    }
                    ?>

                </div>
            </div>
            <!-- /end the lemding receipt -->

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
<?php
include_once "../includes/footer.php";
?>
