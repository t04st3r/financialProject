<?php

session_start();

if($_GET['token'] != $_SESSION['token'] || !isset($_SESSION['id'])){
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}

echo '<h1>welcome '.$_SESSION['user_name'].' ID: '.$_SESSION['id'].'</h1>';

echo '<p><a href="/index.php?logout=true">Log Out</a></p>';


