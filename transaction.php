<?php
session_start();

require_once './db.php';



if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}
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
                        <form method="POST">
                            <div class="form-group">
                                <label for="selectAccount">Select Your Account</label>
                                <select class="form-control" id="selectAccount">
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
                                    <input type="number" class="form-control" id="InputAmount" placeholder="Amount" min="100" max="50000">
                                    <div class="input-group-addon">.00</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="beneficiaryField">Beneficiary Name</label>
                                <input type="text" class="form-control" id="beneficiaryField" placeholder="Beneficiary Name">
                            </div>
                            <div class="form-group">
                                <label for="beneficiaryAccountField">Beneficiary Account</label>
                                <input type="number" class="form-control" id="beneficiaryAccountField" placeholder="Beneficiary Account">
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

