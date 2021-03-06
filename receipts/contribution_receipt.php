<?php
/**
 * Created by PhpStorm.
 * User: kh1m
 * Date: 4/19/2017
 * Time: 7:34 PM
 */
$page_title = "Contribution Receipt";
include_once '../includes/header.php';
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

        // this code handles the contribution_receipt



        if (isset($_SESSION['uname'])) {
            $username = $_SESSION['uname'];

            // first we get the users details from the database
            $RegDate    =   Sharina::getRegistrationDate($username);
            $explodedDate = explode('@', $RegDate);
            $Date  = $explodedDate[0];

            $memberNo   =   Sharina::getMemberNumber($username);
            $selectAllContributions =   dbconnect()->prepare("SELECT * FROM contributions WHERE user_ID=:memberNo");
            $selectAllContributions->execute(array(":memberNo"=>$memberNo));
            $rows   = $selectAllContributions->fetchAll();
            ?>

            <!-- The registration receipt -->
            <div class="ui-dialog" title="Contribution Receipt" style="width:80%;margin:1% 10% 5% auto;position: relative;">
                <div class="media-heading" style="font-weight: bolder;font-size: larger;color:red;text-decoration: underline">Contribution Receipt</div>
                <hr/>
                <div class="">
                    <div class="form-inline">
                        Full Names:
                        <input  type="text" disabled value="<?php echo Sharina::getMemberNames($username); ?>" class="for-control" />

                        <span>Registration Date:</span>
                        <input type="text" disabled value="<?php echo $Date; ?>" class="formcontrol" />
                    </div>
                    <hr/>
                    <?php
                    // if user has no contributions yet, show a message
                    if ($selectAllContributions->rowCount() == 0){
                        ?>
                        <div class="alert alert-info"><i class="ui-icon ui-icon-alert"></i><?php echo NO_CONTRIBUTIONS_YET; ?> </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table-hover table-bordered">
                                    <tr class="text-center danger bolder">
                                        <td>Amount</td>
                                        <td>Date</td>
                                        <td>Event</td>
                                        <td>Transaction Code</td>
                                    </tr>
                                    <?php
                                    foreach ($rows as $columnArr)
                                    {
                                        ?>
                                        <tr class="text-center" title="<?php echo $columnArr['event']; ?>">
                                            <td>KSh:&nbsp;<?php echo $columnArr['amount'] ?></td>
                                            <td><?php echo $columnArr['date']; ?></td>
                                            <td><?php echo $columnArr['event']; ?></td>
                                            <td><?php echo $columnArr['transactioncode']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                            </div><!-- //col-sm-6 -->
                            <div class="col-lg-6" style="padding-left: 17%;">
                                <green>
                                    The total amount you have contributed so far is:<br/>
                                    KSHs: <?php echo Sharina::GetTotalContributions($username); ?>
                                </green>
                            </div>
                        </div>

                        <?php
                    }
                    ?>

                </div>
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
