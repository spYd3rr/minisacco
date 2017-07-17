<?php
/**
 * Created by PhpStorm.
 * User: kh1m Blancos (briansiranga@gmail.com)
 * 0702521614
 * Date: 4/19/2017
 * Time: 8:44 PM
 */

$page_title = "Lending Receipt";
include_once '../includes/header.php';

/**
 * we only the sensitive data to users who are logged in only
 * so we check first if the user is logged in,
 * else show a message
 */

// this code handles the contribution_receipt
?><!-- start body content -->
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


            <!-- The lending receipt -->
            <div id="registration-receipt" class="col-lg-12 ui-dialog" title="Lending Receipt" style="width:80%;margin:1% 10% 5% 10%;position: relative;">
                <div class="media-heading" style="font-weight: bolder;font-size: larger;color:red;text-decoration: underline">Lending Receipt</div>
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
                        <table class="table-hover table-bordered" style="margin:0 0 15px 0;">
                            <tr class="text-center danger bolder">
                                <td>Date Requested</td>
                                <td>Amount Requested</td>
                                <td>Duration</td>
                                <td>Amount payable</td>
                                <td>Installment</td>
                                <td>Reason Given</td>
                            </tr>
                            <?php
                            foreach ($rows as $columnArr)
                            {
                                ?>
                                <tr>
                                    <td><?php echo $columnArr['date']; ?></td>
                                    <td>KSh:&nbsp;<?php echo $columnArr['amount_requested'] ?></td>
                                    <td><?php echo $columnArr['duration'] ?>&nbsp;Months</td>
                                    <td>Ksh:&nbsp;<?php echo $columnArr['payable'] ?></td>
                                    <td>Ksh:&nbsp;<?php echo $columnArr['installment'] ?></td>
                                    <td><?php echo $columnArr['reason'] ?></td>
                                </tr>
                                <?php
                            }

                            ?>
                        </table>
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
