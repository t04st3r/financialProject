<!DOCTYPE html>
<?php
if (isset($_GET['logout']) && $_GET['logout'] = true) {
    logout();
}
session_start();
$character = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$hidden_random = str_shuffle($character);
$_SESSION['hidden_check'] = $hidden_random;

function random_matrix() {
    $secure_code_array = Array('a11' => false, 'a12' => false, 'a13' => false,
        'a21' => false, 'a22' => false, 'a23' => false,
        'a31' => false, 'a32' => false, 'a33' => false);
    $counter = 0;
    while ($counter < 4) {
        $i = rand(1, 3);
        $j = rand(1, 3);
        $random_index = 'a' . $i . $j;
        if (!$secure_code_array[$random_index]) {
            $secure_code_array[$random_index] = true;
            $counter++;
        }
    }
    return $secure_code_array;
}

function logout() {
    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/main.css">
        <title></title>
    </head>
    <body>
        <div id="logo">
            <img src="img/u12.png" class="img-responsive center-block logo-image" alt="Bank Logo">
            <h2>BLACK HORSE</h2>
            <h3>Personal E-Banking System</h3>
        </div>

        <div id="background">
            <form class="form-horizontal" method="POST" action="check_login.php">
                <div id ="form-container" class="col-lg-5 col-lg-offset-3">
                    <div class="form-group">
                        <label for="inputUserName" class="col-sm-2 control-label">Customer UserName</label>
                        <div class="col-sm-10">
                            <input type="text" name="user_name" class="form-control" id="inputUserName" placeholder="Customer User Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" name="pass" class="form-control" id="inputPassword" placeholder="Password" required>
                            <input type="hidden" value="<?php echo $hidden_random ?>" name="hidden_check">
                        </div>
                    </div>
                    <?php
                    //write the authentication failure message 
                    if (isset($_GET['err'])) {
                        if ($_GET['err'] == 'auth') {
                            echo '<h4 class="login_error">Authentication Failed</h4>';
                        }
                        if ($_GET['err'] == 'code') {
                            echo '<h4 class="login_error">Invalid Secure Code</h4>';
                        }
                    }
                    $array = random_matrix();
                    //pass the array for checking correct indexes in check_login.php
                    $_SESSION['matrix_array'] = $array;
                    ?>
                    <label for="matrix" class="col-sm-2 control-label">Secure Code</label>
                    <div class="table-responsive form-group">
                        <table class="table table-bordered" id="matrix">
                            <tr class="info">
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a11" class="control-label">1</label>
                                        <?php if ($array["a11"]) { ?>
                                            <input type="text" name="a11" id="a11" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a11" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a12" class="control-label">2</label>
                                        <?php if ($array["a12"]) { ?>
                                            <input type="text" name="a12" id="a12" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a12" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a13" class="control-label">3</label>
                                        <?php if ($array["a13"]) { ?>
                                            <input type="text" name="a13" id="a13" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a13" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="info">
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a21" class="control-label">4</label>
                                        <?php if ($array["a21"]) { ?>
                                            <input type="text" name="a21" id="a21" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a21" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a22" class="control-label">5</label>
                                        <?php if ($array["a22"]) { ?>
                                            <input type="text" name="a22" id="a22" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a22" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a23" class="control-label">6</label>
                                        <?php if ($array["a23"]) { ?>
                                            <input type="text" name="a23" id="a23" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a23" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <tr class="info">
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a31" class="control-label">7</label>
                                        <?php if ($array["a31"]) { ?>
                                            <input type="text" name="a31" id="a31" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a31" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a32" class="control-label">8</label>
                                        <?php if ($array["a32"]) { ?>
                                            <input type="text" name="a32" id="a32" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a32" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-lg-7 col-lg-offset-2 matrix-table-cell">
                                        <label for="a33" class="control-label">9</label>
                                        <?php if ($array["a33"]) { ?>
                                            <input type="text" name="a33" id="a33" class="form-control matrix_margin enabled_matrix" maxlength="1" required>
                                        <?php } else { ?>
                                            <input type="text" id="a33" class="form-control" disabled="true">
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <div class="login-button col-lg-8">
                        <button type="submit" class="btn btn-default" style="width:200px;">Sign in</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="footer">
            <p>Powered by <strong>FinDev&trade;</strong></p>
        </div>
        
        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
