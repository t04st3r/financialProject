<?php
session_start();
require_once './db.php';



if ($_GET['token'] != $_SESSION['token'] || !isset($_SESSION['id'])) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
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
        echo '<p id="user_message">Welcome ' . $customer_name . '! <a href="/index.php?logout=true">Log Out</a></p>';
        require_once './modules/menubar.php';
        $account_card_array = $db->getAccountCards($id);
        ?>

        <div id="background">
            <div id="card_container">
                <?php
//print cards no. account no. balance and card logo based on query result stored in $account_card_array
//associative array
                $space = 0;
                foreach ($account_card_array as $key => $value) {
                    $inline_left = $space > 0 ? 'left:' . $space . 'px;' : '';
                    echo '<span class="card" style="z-index:1;' . $inline_left . '">';
                    $card_number_formatted = '<strong>' . chunk_split($value['card_number'], 4, ' ') . '</strong>';
                    echo '<span class="card_number">Card Number: ' . $card_number_formatted . '</span>';
                    echo '<span class="account_number">Account Number: ' . $value['account_number'] . '</span>';
                    $balance_value_class = $value['balance'] > 0 ? 'balance_value_green' : 'balance_value_red';
                    $balance = $value['balance'] > 0 ? '+' . number_format($value['balance'], 2, '.', ',') : '-' . number_format($value['balance'], 2, '.', ',');
                    echo '<span class="balance">Balance: <span class="' . $balance_value_class . '">' . $balance . ' ' . $value['currency'] . '</span></span>';
                    $card_logo_path = strncmp($value['circuit'], 'visa', 4) == 0 ? '/img/Old_Visa_Logo.svg.png' :
                            '/img/MasterCard_Logo.svg.png';
                    echo '<img class="circuit_logo" src="' . $card_logo_path . '" style="z-index:2;"/></span>';
                    $space += 380;
                }
                ?>
            </div>
            <div id="menu_buttons">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <span class="button_menu">
                                    <img src="/img/u32.png">
                                    <p>Bank Shop</p>
                                </span>
                            </td>
                            <td>
                                <span class="button_menu">
                                    <img src="/img/u32.png">
                                    <p>Loan and Mortgages</p>
                                </span>
                            </td>
                            <td>
                                <span class="button_menu">
                                    <img src="/img/u32.png">
                                    <p>Financial Products</p>
                                </span>
                            </td>
                            <td>
                                <span class="button_menu">
                                    <img src="/img/u32.png">
                                    <p>Account Detail</p>
                                </span>
                            </td>
                            <td>
                                <span class="button_menu">
                                    <img src="/img/u32.png">
                                    <p>Bills Payment</p>
                                </span>
                            </td>
                            <td>
                                <span class="button_menu">
                                    <img src="/img/u32.png">
                                    <p>Credit Card Payments</p>
                                </span>
                            </td>
                            <td>
                                <span class="button_menu">
                                    <img src="/img/u32.png">
                                    <p>Money Transfer</p>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        require_once './modules/footer.php';
        ?>

        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>

    </body>    