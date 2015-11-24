<?php
session_start();

require_once './db.php';



$id = isset($_SESSION['id']) ? $_SESSION['id'] : 'unknown';
$db = new db();

if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    $db->writeLog('Transaction', 'Token and session check failed for transaction.php page user ID: ' . $id);
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}
if (!isset($_SESSION['code'])) {
    $db->writeLog('Transaction', 'Successful Transaction code not found while rendering page tran_ success.php, ID: ' . $id);
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}

$token = $_GET['token'];
$cust_account = isset($_GET['cust_account']) ? $_GET['cust_account'] : '';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '';
$ben_name = isset($_GET['ben_name']) ? $_GET['ben_name'] : '';
$ben_surname = isset($_GET['ben_surname']) ? $_GET['ben_surname'] : '';
$ben_account = isset($_GET['ben_account']) ? $_GET['ben_account'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';


$code = $_SESSION['code'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        require_once './modules/header.php';
        ?>
        <link rel="stylesheet" href="css/transaction.css">
    </head>
    <body>
        <?php
        require_once './modules/logo.php';
        $customer_name = $db->getUserNameSurname($id);
        echo '<p id="user_message">Logged As: ' . $customer_name . ' <a href="/index.php?logout=true">Log Out</a></p>';
        require_once './modules/menubar.php';
        ?>

        <div id="background">
            <div class="success_message">
                <h4>Transaction successfully executed, transaction code: <?php echo $code; ?></h4>
                <h5><a href="/pdf.php?token=<?php
                    echo $token . '&cust_account=' . $cust_account .
                    '&amount=' . $amount . '&ben_name=' . $ben_name . '&ben_surname=' . $ben_surname . '&ben_account=' . $ben_account . ''
                    . '&message=' . $message . '&code=' . $code . '&cust_name=' . $customer_name
                    ?>" target="_blank">Download PDF Receipt</a></h5>
            </div>
        </div>        

        <?php
        require_once './modules/footer.php';
        ?>

        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>

    </body>
</html>