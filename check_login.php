<?php

session_start();

if (isset($_POST['hidden_check'])) {
    if ($_POST['hidden_check'] == $_SESSION['hidden_check']) {
        
    } else {
        session_abort();
        header('index.php');
    }
}
    