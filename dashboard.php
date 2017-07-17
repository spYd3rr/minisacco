<?php

$page_title = "Dashboard";
include_once 'includes/database.php';
include_once "includes/header.php";

if(Sharina::getSessionState() == False)
{
    header ("Location: index.php?action=notLoggedin");
}
$username = $_SESSION['uname'];
?>

<!-- start body content -->
<div class="body">
    <div class="dashboard" style="position: relative">
        <div class="row" style="word-wrap: break-word">
            <p class="choose col-lg-3">
                <a href="receipts/contribution_form.php"><button class="btn btn-primary form-control">Contribution Form</button></a> <br/><br/>
                <?php
                if(Sharina::CheckPendingLoan($username) == False):
                    ?>
                    <a href="receipts/lending_form.php"><button class="btn btn-primary form-control">Lending Form</button></a> <br/><br/>
                <?php else:?>
                    <a href="receipts/lending_form.php" title="No Current Loan"><button class="btn btn-primary form-control" title="You have a pending Loan" disabled>Lending Form</button></a><br/><br/>
                    <?php
                endif;
                ?>
                <a href="receipts/withdrawalForm.php" class="btn btn-primary form-control">Withdrawal Form</a>
            </p>
            <p class="col-lg-3">
                <a href="receipts/registration_receipt.php" class="btn btn-primary form-control ui-corner-all">
                    Registration Receipt</a> <br/><br/>
                <a href="receipts/contribution_receipt.php" class="btn btn-primary form-control">Contribution Receipt</a> <br/><br/>
                <a href="receipts/lending_receipt.php" class="btn btn-primary form-control">Lending Receipt</a><br/><br/>
                <a href="receipts/WithdrawalHistory.php" class="btn btn-primary form-control">Withdrawal History</a>
            </p>
            <div class="col-lg-3 form-group">
                <a href="receipts/YearlyContributions.php" class="btn btn-primary form-control">Yearly Contributions</a> <br/><br/>
                <a href="receipts/LoanStatement.php" class="btn btn-primary form-control">Loan statement</a> <br/><br/>
                <a href="receipts/MemberContributionStatement.php" class="form-control btn btn-primary">Contribution Statement</a><br/><br/>
            </div>
            <p class="col-lg-3">
                <a href="receipts/lendingHistory.php"><button class="btn btn-primary form-control">Lending History</button></a><br/><br/>
                            <?php
                            if(Sharina::CheckPendingLoan($username) == true):
                            ?>
                            <a href="receipts/RepayLoan.php"><button class="btn btn-primary form-control">Repay Loan</button></a><br/><br/>
                                <?php else:?>
                            <a href="receipts/RepayLoan.php" title="No Current Loan"><button class="btn btn-primary form-control" title="No Current Loan" disabled>Repay Loan</button></a><br/><br/>
                        <?php
                          endif;
                        ?>
            </p>
        </div>
    </div>
</div>



<!-- Testing is being done here  -->





<!-- end body content -->
<?php
include_once "includes/footer.php";
?>