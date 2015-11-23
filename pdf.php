<?php

session_start();

$id = isset($_SESSION['id']) ? $_SESSION['id'] : 'unknown';

if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    $db->writeLog('Transaction', 'Token and session check failed for pdf.php page user ID: ' . $id);
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}

include './mpdf60/mpdf.php';

$cust_account = isset($_GET['cust_account']) ? $_GET['cust_account'] : ''; 
$amount = isset($_GET['amount']) ? $_GET['amount'] : '';
$ben_name = isset($_GET['ben_name']) ? $_GET['ben_name'] : '';
$ben_surname = isset($_GET['ben_surname']) ? $_GET['ben_surname'] : '';
$ben_account = isset($_GET['ben_account']) ? $_GET['ben_account'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';
$customer_name = isset($_GET['cust_name']) ? $_GET['cust_name'] : ''; 

//don't ask me why function date are give me the wrong date only in this part of the code..
//and need to setup the correct timezone
date_default_timezone_set('Asia/Hong_Kong');
$date_time = date('Y-m-d H:i:s');

$mpdf=new mPDF();
$html = '<div style="text-align:center;margin-top:3px;">'
        . '<img src="img/u12.png"><h3>Black Horse E-Banking</h3><h6>Headquarter and Main Office:</h6><h6>Sir Run Run Shaw Buidings, Kowloon Tong, Hong Kong +852-314159265</h6>'
        . '</div><br/><br/>'
        . '<div style="text-align:justify"><p>Dear Customer,</p>'
        . '<p>Here we report the details of your transaction, wishing you a good day.</p>'
        . '<hr>'
        . '<p><strong>Date and time:</strong> '.$date_time.'</p>'
        . '<p><strong>Customer Name:</strong> '.$customer_name.'</p>'
        . '<p><strong>Transferring Account:</strong> '.$cust_account.'</p>'
        . '<p><strong>Tranferred Amount:</strong> '.$amount.'.00 $HK</p>'
        . '<p><strong>Beneficiary Name:</strong> '.$ben_name.' '.$ben_surname.'</p>'
        . '<p><strong>Beneficiary Account:</strong> '.$ben_account.'</p>'
        . '<p><strong>Transaction Code:</strong> '.$code.'</p>'
        . '<p><strong>Message for the Beneficiary:</strong> '.$message.'</p></div>'
        . '<hr>';



$mpdf->WriteHTML($html);
$mpdf->Output();
exit;

