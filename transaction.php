<?php
session_start();

require_once './db.php';



if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}


if (isset($_POST['ben_account']) && isset($_POST['customer_account']) && isset($_POST['amount']) && isset($_POST['ben_name']) && isset($_POST['ben_surname']) && isset($_POST['ben_account'])) {

    //filtering input data to prevent SQL injection
    $cust_account = filter_input(INPUT_POST, 'customer_account', FILTER_SANITIZE_SPECIAL_CHARS);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_SPECIAL_CHARS);
    $ben_name = filter_input(INPUT_POST, 'ben_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $ben_surname = filter_input(INPUT_POST, 'ben_surname', FILTER_SANITIZE_SPECIAL_CHARS);
    $ben_account = filter_input(INPUT_POST, 'ben_account', FILTER_SANITIZE_SPECIAL_CHARS);
    $message = isset($_POST['message']) ? filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS) : '';
    $ip = $_SERVER['REMOTE_ADDR'];
    $cust_id = $_SESSION['id'];

    $db = new db;
    $result = $db->transaction($cust_id, $cust_account, $amount, $ben_name, $ben_surname, $ben_account, $message, $ip);
}

$error = !$result['result'];
$message = $result['message'];
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
$id = $_SESSION['id'];
$db = new db();
$customer_name = $db->getUserNameSurname($id);
echo '<p id="user_message">Logged As: ' . $customer_name . ' <a href="/index.php?logout=true">Log Out</a></p>';
require_once './modules/menubar.php';
$account_number_array = $db->getAccountCards($id);
?>
        <div id="background">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4 col-lg-offset-4 formContainer">
                        <h4 class="<?php $class = $error ? 'error' : 'no_error';
        echo $class; ?>"><?php echo $message; ?></h4>
                        <form method="POST">
                            <div class="form-group">
                                <label for="selectAccount">Select Your Account</label>
                                <select class="form-control" id="selectAccount" name="customer_account">
<?php
foreach ($account_number_array as $key => $value) {
    echo '<option value="' . $value['account_number'] . '">' . $value['account_number'] . '</option>';
}
?>
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="InputAmount">Amount (in Hong Kong dollar)</label>
                                <div class="input-group">
                                    <div class="input-group-addon">$HK</div>
                                    <input type="number" name="amount" class="form-control" id="InputAmount" placeholder="Amount" min="100" max="500000" required>
                                    <div class="input-group-addon">.00</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="beneficiaryField">Beneficiary Name</label>
                                <input type="text" name="ben_name" class="form-control" id="beneficiaryField" placeholder="Beneficiary Name" requred>
                            </div>
                            <div class="form-group">
                                <label for="beneficiaryField">Beneficiary Surname</label>
                                <input type="text" name="ben_surname" class="form-control" id="beneficiaryField" placeholder="Beneficiary Surname" required>
                            </div>
                            <div class="form-group">
                                <label for="beneficiaryAccountField">Beneficiary Account</label>
                                <input type="number" name="ben_account" class="form-control" id="beneficiaryAccountField" placeholder="Beneficiary Account" required>
                            </div>
                            <div class="form-group">
                                <label for="beneficiaryAccountField">Message for the Beneficiary</label>
                                <textarea name="message" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">Transfer Money</button>
                        </form>
                    </div>
                </div> 
            </div>
        </div>
<?php
require_once './modules/footer.php';
?>

        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>

    </body>

