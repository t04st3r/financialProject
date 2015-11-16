<?php

session_start();
require_once './db.php';



if($_GET['token'] != $_SESSION['token'] || !isset($_SESSION['id'])){
    if($_GET['token'] != $_SESSION['token']){
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=token');
    }
    if(!isset($_SESSION['id'])){
         header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=id');
    }
}

?>
<html>
    <head>
        <?php 
        require_once './modules/header.php';
        ?>
        <link rel="stylesheet" href="css/welcome.css">
    </head>
    <body>
        <?php
        require_once './modules/logo.php';
        $id = $_SESSION['id'];
        $db = new db();
        $customer_name = $db->getUserNameSurname($id);
        echo '<p id="user_message">Welcome '.$customer_name.'! <a href="/index.php?logout=true">Log Out</a></p>';
        require_once './modules/menubar.php';
        ?>
        
        <div id="background">
            <div id="card_container">
                <span class="card">DIOCANE<img class="circuit_logo" src="/img/Old_Visa_Logo.svg.png"/></span>
                <span class="card" style="left:380px;">DIOCANE</span>
                <span class="card">DIOCANE</span>
            </div>
            <div id="menu_buttons">
                
            </div>
        </div>
        
        <?php 
        require_once './modules/footer.php';
        ?>
        
        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>
        
    </body>    