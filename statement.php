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
        <link rel="stylesheet" href="css/statement.css">
    </head>
    <body>
        <?php
        require_once './modules/logo.php';
        $id = $_SESSION['id'];
        $db = new db();
        $customer_name = $db->getUserNameSurname($id);
        echo '<p id="user_message">Logged As: ' . $customer_name . ' <a href="/index.php?logout=true">Log Out</a></p>';
        require_once './modules/menubar.php';
        ?>

        <div id="background">

            <h4 class="search_title">Search Option</h4>
                        <form class="form-inline">
                            <div class="form-group">
                                <label for="selectAccount">Account</label>
                                <select class="form-control" id="selectAccount" name="customer_account">
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
                                <input type="date" class="form-control" id="startDate" max="<?php echo $date; ?>">
                            </div>
                            <div class="form-group">
                                <label for="endDate">End Date</label>
                                <input type="date" class="form-control" id="endDate" max="<?php echo $date; ?>">
                            </div>
                            <div class="form-group">
                                <label for="startAmount">Min</label>
                                <input type="number" class="form-control" id="startAmount" placeholder="Min Amount" min="0">
                            </div>
                            <div class="form-group">
                                <label for="startAmount">Max</label>
                                <input type="number" class="form-control" id="startAmount" placeholder="Max Amount" min="0">
                            </div>
                            <button type="submit" class="btn btn-default">Search</button>
                        </form>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 formContainer">

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
                                        Account No.
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
                                <tr>
                                    <td>
                                        Transaction Code
                                    </td>
                                    <td>
                                        Date and Time
                                    </td>
                                    <td>
                                        Account No.
                                    </td>
                                    <td>
                                        Message
                                    </td>
                                    <td>
                                        Amount
                                    </td>
                                </tr>
                            </tbody>
                        </table>

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