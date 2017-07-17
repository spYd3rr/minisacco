<?php

$page_title = "Members";
include_once "includes/header.php";
if(!isset($_SESSION['loggedin']))
{
    header ("Location: index.php");
}

?>

<div class="body">
    <p>
        These are our members:<br/>
    </p>
    <div class="row">
        <div class="col-lg-8 ui-dialog" style="margin:0 5% 5% 5%;width:90%;position: relative">
            <table class="table-bordered table-hover">
                <tr class="danger">
                    <td class="text-center">MEM. NO.</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FIRSTNAME</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LASTNAME</td>
                    <td class="text-center">&nbsp;&nbsp;&nbsp;USERNAME</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EMAIL</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID Number</td>

                </tr>

                <?php
                try{
                    $stmt = dbconnect()->prepare("SELECT * FROM users WHERE userLevel != :admin ORDER BY ID ASC ");
                    $stmt->execute(array(":admin" => 'admin'));
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $rows    = $stmt->rowCount();
                    foreach($results as $row)
                    {
                        $ID = $row['ID'];
                        $firstname = $row['firstname'];
                        $lastname = $row['lastname'];
                        $username = $row['username'];
                        $email = $row['email'];
                        $nationalID = $row['nationalID'];
                        ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ID; ?></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ucfirst($firstname); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ucfirst($lastname); ?></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $username;?></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $email;?></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $nationalID;?></td>
                            <?php if(isset($_SESSION['admin'])):?>
                                <td><a href="members.php?action=deleteUser&userID=<?php echo $ID; ?>"><img src="assets/images/remove.png" title="Delete User"></a></td>
                            <?php endif;?>

                        </tr>
                        <?php
                    }
                }  catch(PDOException $e){
                    echo $e->getMessage();
                }


                ?>
            </table>
        </div>
    </div>

</div>

<?php
// delete user functon
if(isset($_GET['action']) && $_GET['action'] = 'deleteUser' && isset($_GET['userID']))
{
    $stmt = dbconnect()->prepare("SELECT * FROM users WHERE ID = :ID");
    $stmt->execute(array(":ID" => $_GET['userID']));

    // that user does not exst
    if($stmt->rowCount() == 0)
    {
        redirect('index.php');
    }
    else{
        // delete the user
        $stmt = dbconnect()->prepare("DELETE FROM users WHERE ID = :ID");
        $stmt->execute(array(":ID" => $_GET['userID']));
        redirect("Location: members.php");
    }

}

include_once "includes/footer.php";
?>
