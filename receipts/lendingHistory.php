<?php
/**
 * Created by PhpStorm.
 * User: kH1m
 * Date: 4/26/2017
 * Time: 8:33 PM
 */

$page_title = "Lending History";
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
            $selectAllLendings =   dbconnect()->prepare("SELECT * FROM lending WHERE user_ID=:memberNo");
            $selectAllLendings->execute(array(":memberNo"=>$memberNo));
            $rows   = $selectAllLendings->fetchAll();
        ?>
        <!-- The loan statement receipt -->
        <div id="registration-receipt" class="col-lg-12 ui-dialog" title="Loan Statement" style="width:80%;margin:1% 10% 5% 10%;position: relative;">
            <div class="media-heading" style="font-weight: bolder;font-size: larger;color:red;text-decoration: underline">Lending History</div>
            <hr/>
            <div class="form-inline">
                Full Names:
                <input  type="text" disabled value="<?php echo Sharina::getMemberNames($username); ?>" class="for-control" />

                <span>Registration Date:</span>
                <input type="text" disabled value="<?php echo $Date; ?>" class="formcontrol" />
            </div>
            <hr/>
            <?php
        if (!isset($_GET['loanID']))
        {
            // if user has no contributions yet, show a message
            if ($selectAllLendings->rowCount() == 0){
                ?>
                <div class="alert alert-info"><i class="fa fa-user"></i><?php echo NO_LOAN_EVER; ?> </div>
                <?php
            }
            ?>
            <div class="loan-chooser">
                <?php
            // we ask the user to chose the loan details they wanna see
            foreach ($rows as $item) {
                $loanId = $item['ID'];
                $requested = $item['amount_requested'];
                $ReqDateTime = $item['date'];
                $explodedReqDateTime = explode(':',$ReqDateTime);
                $regDate = $explodedReqDateTime[0];
                $regTime = $explodedReqDateTime[1];
                $reason  = $item['reason'];
                ?>
                <div>
                    <span>
                        On <green><?php echo $regDate; ?></green> you requested <green>KSh. <?php echo $requested;?></green> and stated your reason as
                        <green><?php echo $reason;?></green>
                    </span>
                    <br/>
                    <span>
                        <a href='receipts/lendingHistory.php?loanID=<?php echo $loanId; ?>' class="btn btn-info">Loan Details</a>
                        </span>
                </div>
                <div class="border-bottom"></div>

                <?php
            }
                ?>
            </div>
            <?php
        }
        elseif(isset($_GET['loanID']))
        {
            $loadID = $_GET['loanID'];
            // the user is viewing the loan whose ID is in the URL
            ?>
            <div>
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
                            // select the loan using the loanID from the URL
                            $sth = dbconnect()->prepare("SELECT * FROM lending WHERE ID=:loanID AND user_ID=:user_ID");
                            $sth->execute(array(
                                ":loanID"   => $loadID,
                                ":user_ID"  => Sharina::getMemberNumber($username),
                            ));
                            $results = $sth->fetch(PDO::FETCH_ASSOC);

                            if ($sth->rowCount() > 0)
                            {
                                //if the user really has a loan as per the loanID provided
                                $amount = $results['amount_requested'];
                                $duration = $results['duration'];
                                $installment = $results['installment'];
                                $payable = $results['payable'];
                                $LendingDateTime = $results['date'];
                                $explodedDateTime = explode(':', $LendingDateTime);
                                $lendingDate = $explodedDateTime[0];
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
                                    <td><?php echo $lendingDate; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="danger bolder">Reason Given</td>
                                    <td><?php echo $results['reason'] ?></td>
                                </tr>
                                </table>

                                <?php
                                // we get the payment history and show it here if the user has not yet completed paying
                                try{
                                    $stmt = dbconnect()->prepare("SELECT * FROM repays WHERE user_ID=:user_ID AND loanID=:loanID");
                                    $stmt->execute(array(
                                        ":user_ID"  => Sharina::getMemberNumber($username),
                                        ":loanID"   => $loadID,
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
                                        <blue>Your loan repayment history</blue><br/>
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
                    else{
                        // the loanID from the URl does not correspond ith this user
                        ?>
                        <div  title="Not your Loan!!">
                            <p>
                                <?php echo "That loan provided was not requested by you! Can you stop that please!"; ?>
                            </p>
                        </div>
                        <?php
                    }

                }
                ?>

            </div>
            <?php
        }
        else{
            ?>
            <div  title="Action not Recognised">
                <p>
                    <?php echo "That action was not recognised"; ?>
                </p>
            </div>
            <?php
        }
            ?>
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
