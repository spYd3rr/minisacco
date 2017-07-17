<?php


class Sharina
{
    public static function getSessionState()
    {
        if (isset($_SESSION['uname']))
        {
            return true;
        }
        return false;
    }

    public static function getMemberNames($username)
    {
        if (Sharina::getSessionState() == true)
        {
            $getName  =  dbconnect()->prepare("SELECT firstname,lastname FROM users WHERE username=:username OR email=:email");
            $getName->execute(array(
                ":username" => $username,
                ":email"    => $username,
            ));
            $memberNames = $getName->fetch(PDO::FETCH_ASSOC);
            
            $fname = ucfirst($memberNames['firstname']);
            $lname = ucfirst($memberNames['lastname']);
            $fullnames = $fname . ' ' . $lname;
        }
        return $fullnames;
    }

    /**
     * @param $username
     * @return mixed
     */
    public static function getMemberNumber($username)
    {
        if (Sharina::getSessionState() == true)
        {
            $getUID  =  dbconnect()->prepare("SELECT ID FROM users WHERE username=:username OR email=:email");
            $getUID->execute(array(
                ":username" => $username,
                ":email"    => $username,
            ));
            $UID = $getUID->fetchColumn();

        }
        return $UID;
    }

    public static function getReceiptNumber($database)
    {
        $getReceiptNo = dbconnect()->prepare("SELECT * FROM $database");
        $getReceiptNo->execute();
        $rows    =  $getReceiptNo->rowCount();
        $receiptNo    = $rows + 1;

        return $receiptNo;
    }

    public static function getRegistrationDate($username)
    {
        if (Sharina::getSessionState() == true)
        {
            $getRegDate  =  dbconnect()->prepare("SELECT reg_date FROM users WHERE username=:username OR email=:email");
            $getRegDate->execute(array(
                ":username" => $username,
                ":email"    => $username,
            ));
            $FullRegDate = $getRegDate->fetchColumn();
            $explodedDate  = explode('@', $FullRegDate);
            $RegDate = $explodedDate[0];

        }
        return $RegDate;
    }

    public static function RequestLoan($username)
    {
        $amount         =   $_POST['amount'];
        $installment    =   $_POST['installment'];
        $duration       =   DurationDue($amount,$installment);
        $payable        =   TotalPayable($amount,$installment);
        $reason         =   $_POST['reason'];

        $stmt   =   dbconnect()->prepare("INSERT INTO lending (user_ID,amount_requested,duration,payable,installment,date,reason,remaining) 
                                            VALUES (:user_ID,:amount,:duration,:payable,:installment,:date,:reason,:remaining)");
        $stmt->execute(array(
            "user_ID"       => self::getMemberNumber($username),
            "amount"        => $amount,
            ":duration"     => $duration,
            ":payable"      => $payable,
            ":installment"  => $installment,
            ":date"         => TODAY,
            ":reason"       => $reason,
            ":remaining"    => $payable,
        ));

        header("Location: lending_form.php");

    }

    public static function CheckPendingLoan($username)
    {
        try{
            $stmt = dbconnect()->prepare("SELECT paid FROM lending WHERE paid=:paid AND user_ID=:user_ID");
            $stmt->execute(array(":paid" => 0, ":user_ID" => self::getMemberNumber($username) ));
            $rows = $stmt->rowCount();
            if ($rows > 0)
            {
                return true;
            }
        }
        catch (Exception $e)
        {
            echo "Could not get some of your loan details at this time";
        }
        return false;
    }

    public static function GetLoanedDate($username)
    {
        $stmt     = dbconnect()->prepare("SELECT date FROM lending WHERE user_ID=:user_ID AND paid=:status");
        $stmt->execute(array(
            ":user_ID"   => self::getMemberNumber($username),
            ":status"    => 0,
        ));
        $FullLoanDate = $stmt->fetchColumn();
        $explodedDate  = explode(':', $FullLoanDate);
        $LoanedDate = $explodedDate[0];
        
        return $LoanedDate;
        
    }

    /**
     * @param $username
     * @return string
     */
    public static function GetUnpaidAmount($username)
    {
            $stmt = dbconnect()->prepare("SELECT remaining FROM lending WHERE paid=:paid AND ID=:loanID");
            $stmt->execute(array(
                ":paid" => 0,
                ":loanID" => self::GetUnpaidLoanID($username)
            ));
            $remainingAmount = $stmt->fetchColumn();

            return $remainingAmount;
    }

    public static function GetPreferredInstallment($username)
    {
        $stmt = dbconnect()->prepare("SELECT installment FROM lending WHERE paid=:paid AND ID=:loanID");
        $stmt->execute(array(
            ":paid" => 0,
            ":loanID" => self::GetUnpaidLoanID($username)
        ));
        $preferredInstallment = $stmt->fetchColumn();

        return $preferredInstallment;
    }

    public static function GetUnpaidLoanID($username)
    {
        try{
            $stmt = dbconnect()->prepare("SELECT ID FROM lending WHERE user_ID=:user_ID AND paid=:paid");
            $stmt->execute(array(
                ":user_ID"  => self::getMemberNumber($username),
                ":paid"     => 0,
            ));

            $ID = $stmt->fetchColumn();
            return $ID;
        }
        catch (PDOException $e)
        {
            echo $e->getMessage();
        }
        return false;
    }
    
    public static function RepayLoan($username)
    {
        try{
            
            if (self::GetUnpaidAmount($username) > ceil(0)){
                // if the user has a pending loan balance
                $amount         =   $_POST['amount'];
                $remainingAfterPay = self::GetUnpaidAmount($username) - $amount;

                $stmt   =   dbconnect()->prepare("UPDATE lending set remaining=:remaining WHERE user_ID=:user_ID AND paid=:status");

                $stmt->execute(array(
                    ":remaining"    => $remainingAfterPay,
                    ":user_ID"      => self::getMemberNumber($username),
                    ":status"       => 0,
                ));

                $saveRepay = dbconnect()->prepare("INSERT INTO repays (user_ID,amount,date,remaining,loanID) VALUES (:user_ID,:amount,:date,:remaining,:loanID)");
                $saveRepay->execute(array(
                    ":user_ID"  => self::getMemberNumber($username),
                    ":amount"   => $amount,
                    ":date"     => TODAY,
                    ":remaining"=> $remainingAfterPay,
                    ":loanID"   => self::GetUnpaidLoanID($username),
                ));


                //redirect('../dashboard.php');
                return true;
            }
            else{
                return false;
            }

        } catch (Exception $e)
        {
            echo "Could not complete loan repayment at this time";
        }
    }

    public static function GetTotalContributions($username)
    {
        try{
            $stmt = dbconnect()->prepare("SELECT SUM(amount) AS sum FROM contributions WHERE user_ID = :user_ID");
            $stmt->execute(array(":user_ID" => self::getMemberNumber($username)));
            
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $results['sum'];
        }
        catch (PDOException $e)
        {
            echo "Could not get your total contributions at this time.";
        }
        return $stmt;
    }

    public static function GetTotalWithdrawals($username)
    {
        try{
            $stmt = dbconnect()->prepare("SELECT SUM(amount) AS sum FROM withdrawals WHERE user_ID = :user_ID");
            $stmt->execute(array(":user_ID" => self::getMemberNumber($username)));

            $results = $stmt->fetch(PDO::FETCH_ASSOC);

            return $results['sum'];
        }
        catch (PDOException $e)
        {
            echo "Could not get your total contributions at this time.";
        }
        return $stmt;
    }

    public static function GetTotalMoney()
    {
        $stmt = dbconnect()->prepare("SELECT totalamount FROM moneypool WHERE ID = :ID");
        $stmt->execute(array(":ID" => 1));

        return $stmt->fetchColumn();
    }

    public static function GetUsersTotalMoney($username)
    {
        $stmt = dbconnect()->prepare("SELECT totalamount FROM moneypool WHERE user_ID = :ID");
        $stmt->execute(array(":ID" => self::getMemberNumber($username)));

        return $stmt->fetchColumn();
    }
    
    public static function GetTotalMoneyForUser()
    {
        $stmt = dbconnect()->prepare("SELECT totalamount FROM moneypool WHERE ID = :ID");
        $stmt->execute(array(":ID" => 1));

        return $stmt->fetchColumn();
    }

    /**
     * @param $username
     */
    public static function MakeWithdraw($username,$TransactionCode)
    {
        try{
            $amount         = $_POST['amount'];
            $reason         = $_POST['reason'];
            $recordWithdraw = dbconnect()->prepare("INSERT INTO withdrawals (user_ID,amount,date,reason,transactioncode) 
                                                    VALUES (:user_ID,:amount,:date,:reason,:transactioncode)");
            $recordWithdraw->execute(array(
                ":user_ID"  => self::getMemberNumber($username),
                ":amount"   => $amount,
                ":date"     => TODAY,
                ":reason"   => $reason,
                ":transactioncode"   => $TransactionCode,
            ));

            // update the user's moneypool
            $userremainingamount = self::GetUsersTotalMoney($username) - $amount;
            $updateUserTotals   = dbconnect()->prepare("UPDATE moneypool SET totalamount = :userremainingamount WHERE user_ID = :ID");
            $updateUserTotals->execute(array(
                ":userremainingamount" => $userremainingamount,
                ":ID"     => self::getMemberNumber($username),
            ));
            
        }
        catch (PDOException $e)
        {
            echo "Cannot mae the transaction now! Try later.";
        }
    }

    public static function makeContribution($username,$TransactionCode)
    {
        // we will use the session variable as our username to  get which user is making the contribution
        $stmt   =   dbconnect()->prepare("SELECT * FROM users WHERE username=:username OR email=:email");
        $stmt->execute(array(
            ":username" => $username,
            ":email"    => $username,
        ));
        $amount    =  $_POST['amount'];
        $event         = $_POST['event'];
        $date      =  TODAY;
        //we now save the data to the database
        $insertContribution  = dbconnect()->prepare("INSERT INTO contributions (user_ID,amount,date,event,transactioncode) 
                                                      VALUES (:user_ID,:amount,:date,:event,:transactioncode)");
        $insertContribution->execute(array(
            ":user_ID"  => self::getMemberNumber($username),
            ":amount"   => $amount,
            ":date"     => $date,
            ":event"    => $event,
            ":transactioncode" => $TransactionCode,
        ));

        // update user's moneypool
        $userremainingamount = self::GetUsersTotalMoney($username) + $amount;
        $updateTotals   = dbconnect()->prepare("UPDATE moneypool SET totalamount = :remainingamount WHERE user_ID= :user_ID");
        $updateTotals->execute(array(
            ":remainingamount" => $userremainingamount,
            ":user_ID"         => self::getMemberNumber($username),
        ));

        //redirect('../dashboard.php');

    }

    public static function GetUserLevel($username)
    {
        $stmt = dbconnect()->prepare("SELECT userLevel FROM users WHERE userLevel = :userLevel AND username = :username");
        $stmt->execute(array(
            ":ID"       => 'admin',
            ":username" => $username,
        ));

        return $stmt->fetchColumn();
    }


}
