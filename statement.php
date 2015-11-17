<?php
session_start();

require_once './db.php';



if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}

$account = isset($_GET['account']) ? filter_input(INPUT_GET, 'account', FILTER_SANITIZE_SPECIAL_CHARS) : '';
$startDate = isset($_GET['startDate']) ? filter_input(INPUT_GET, 'startDate', FILTER_SANITIZE_SPECIAL_CHARS) : '';
$endDate = isset($_GET['endDate']) ? filter_input(INPUT_GET, 'endDate', FILTER_SANITIZE_SPECIAL_CHARS) : '';
$minAmount = isset($_GET['minAmount']) ? filter_input(INPUT_GET, 'minAmount', FILTER_SANITIZE_SPECIAL_CHARS) : '';
$maxAmount = isset($_GET['maxAmount']) ? filter_input(INPUT_GET, 'maxAmount', FILTER_SANITIZE_SPECIAL_CHARS) : '';

?>


<!DOCTYPE html>
<html>
    <head>
<?php
require_once './modules/header.php';
?>
        <link rel="stylesheet" href="css/statement.css">
    </head>
    <body>
<?php
require_once './modules/logo.php';
$id = $_SESSION['id'];
$db = new db();
$token = $_GET['token'];
$customer_name = $db->getUserNameSurname($id);
$result = $db->search($account, $startDate, $endDate, $minAmount, $maxAmount);
$error = '';
$array_result = array();
if(!$result['result']){
    $error = $result['error'];
}else{
    $array_result = $result['data'];
}
   
echo '<p id="user_message">Logged As: ' . $customer_name . ' <a href="/index.php?logout=true">Log Out</a></p>';
require_once './modules/menubar.php';
?>

        <div id="background">

            <h4 class="search_title">Search Options</h4>
            <form class="form-inline" method="GET">
                <div class="form-group">
                    <label for="selectAccount">Account</label>
                    <select name="account" class="form-control" id="selectAccount" name="customer_account">
<?php
$account_number_array = $db->getAccountCards($id);
foreach ($account_number_array as $key => $value) {
    echo '<option value="' . $value['account_number'] . '">' . $value['account_number'] . '</option>';
}
?>
                    </select>

                </div>
                <div class="form-group">
<?php $date = date('Y-m-d'); ?>
                    <label for="startDate">Start Date</label>
                    <input name="startDate" type="date" class="form-control" id="startDate" max="<?php echo $date; ?>">
                </div>
                <div class="form-group">
                    <label for="endDate">End Date</label>
                    <input name="endDate" type="date" class="form-control" id="endDate" max="<?php echo $date; ?>">
                </div>
                <div class="form-group">
                    <label for="startAmount">Min</label>
                    <input name="minAmount" type="number" class="form-control" id="startAmount" placeholder="Min Amount" min="0">
                </div>
                <div class="form-group">
                    <label for="stopAmount">Max</label>
                    <input type="number" name="maxAmount" class="form-control" id="stopAmount" placeholder="Max Amount" min="0">
                </div>
                <input type="hidden" value="<?php echo $token ?>" name="token">
                <button type="submit" class="btn btn-default">Search</button>
            </form>
            <div class="container-fluid table-container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 formContainer">
                        <h4 class="error"><?php echo $error; ?></h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <td>
                                        Transaction Code
                                    </td>
                                    <td>
                                        Date and Time
                                    </td>
                                    <td>
                                        Transferring Account
                                    </td>
                                    <td>
                                        Beneficiary Account
                                    </td>
                                    <td>
                                        Message
                                    </td>
                                    <td>
                                        Amount
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($array_result as $row){
                                    $class = $row['account'] == $account ? 'error' : 'green';
                                    echo '<tr>'
                                    . '<td>'.$row['code'].'</td>'
                                    . '<td>'.$row['time_date'].'</td>'
                                    . '<td>'.$row['account'].'</td>'
                                    . '<td>'.$row['dest_account'].'</td>'
                                    . '<td>'.$row['message'].'</td>'
                                    . '<td class="'.$class.'">'.number_format($row['amount'], 2, '.', ',').' $HK</td>'
                                            .'</tr>';
                                }
                                ?>
                            </tbody>
                            <?php 
                            $balance = $db->getAccountBalance($account);
                            ?>
                            <tfoot>
                               <tr>
                                   <td colspan="6" class="tfooter">
                                        Total: <?php echo number_format($balance, 2, '.', ',').' $HK'; ?> 
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

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
</html>