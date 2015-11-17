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

        <div id="background"></div>

        <?php
        require_once './modules/footer.php';
        ?>

        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>

    </body>
</html>