<?php
/**
 * Created by PhpStorm.
 * User: kH1m
 * Date: 4/25/2017
 * Time: 11:16 AM
 */

$page_title = "Yearly Contributions";
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
    $selectAllLendings =   dbconnect()->prepare("SELECT * FROM contributions WHERE user_ID=:memberNo");
    $selectAllLendings->execute(array(":memberNo"=>$memberNo));
    $rows   = $selectAllLendings->fetchAll();
    ?>


            <!-- The loan statement receipt -->
            <div id="registration-receipt" class="col-lg-12 ui-dialog" title="Yearly Contributions" style="width:80%;margin:1% 10% 5% 10%;position: relative;z-index:0;">
                <div class="media-heading" style="font-weight: bolder;font-size: larger;color:red;text-decoration: underline">Yearly Contributions</div>
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
                        <div class="alert alert-info"><i class="ui-icon ui-icon-alert"></i><?php echo NO_CONTRIBUTION_HISTORY; ?> </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="col-md-6">
                            <div style="margin:0 0 15px 0;">
                                <?php
                                foreach ($rows as $columnArr)
                                {
                                    $dateTime = $columnArr['date'];
                                    $explodedDate = explode(' ',$dateTime);
                                    $date = $explodedDate[0];
                                    ?>
                                    <div style="line-height: 33px;">
                                        <span class="danger bolder">Amount Contributed</span>
                                        <input type="text" size="26" disabled value="KSh:&nbsp;<?php echo $columnArr['amount']; ?>" />
                                    <br/>
                                        <span class="danger bolder">Date of contribution</span>
                                        <input type="text" size="25" disabled value="<?php echo $date; ?>" />
                                        <br/>
                                        <span class="danger bolder">Transacton Code</span>
                                        <input type="text" size="30" disabled value="<?php echo $columnArr['transactioncode']; ?>" />
                                    </div>
                                    <hr/>
                                    <?php
                                }
                                ?>
                            </div>
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
