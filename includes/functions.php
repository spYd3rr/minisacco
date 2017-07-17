<?php


function redirect($url)
{
    header("Location: $url");
}

/**
 * this will hold our lending functions
 */

function calculateMonthlyInterest($amount)
{
    $MonthlyInterest =  (SHARINA_INTEREST / 100) * $amount;

    return ceil($MonthlyInterest);
}

function TotalInterest($amount,$installment)
{
    $DurationWithoutInterest = $amount / $installment;

    $TotalInterest  =   $DurationWithoutInterest * calculateMonthlyInterest($amount);
    
    return $TotalInterest;
}

function TotalPayable($amount, $installment)
{
    $TotalPayable   =   $amount + TotalInterest($amount,$installment);
    
    return ceil($TotalPayable);
}

function DurationDue($amount,$installment)
{
    $DurationDue    =  TotalPayable($amount,$installment) / $installment; 
    
    return ceil($DurationDue);
}

function AllowedInstallment($amount, $installment)
{
    $allowedInstalment = (SHARINA_INTEREST / 100) * $amount;

    if ($installment < $allowedInstalment)
    {
        return false;
    }
    return true;
}

function GenerateTransactionCode($length)
{
    $alphabets          = range('A','Z');
    $numbers            = range('0','9');
    $final_array         = array_merge($alphabets,$numbers);
    $rand               = '';
    while ($length--)
    {
        $key = array_rand($final_array);
        $rand .= $final_array[$key];
        $hash = hash('sha1',$rand);
        $hash = strip_tags(stripslashes($hash));
    }
    $rand2 = strtoupper(substr($hash,20,$length));
    return 'SHAR' . $rand2;
}