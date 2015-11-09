<!DOCTYPE html>
<?php 
    

    require_once 'db.php';
    require_once 'check_login.php';

$character = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$hidden_random = str_shuffle($character);

$_SESSION['hidden_check'] = $hidden_random;
    
?>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <title></title>
    </head>
    <body>
        <form class="form-horizontal" method="POST">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Customer User Name</label>
                <div class="col-sm-10">
                    <input type="text" name="user_name" class="form-control" id="inputEmail3" placeholder="Customer User Name" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" name="pass" class="form-control" id="inputPassword3" placeholder="Password" required>
                    <input type="hidden" value="<?php echo $hidden_random?>" name="hidden_check">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Sign in</button>
                </div>
            </div>
        </form>
        <script src="js/bootstrap.min.js"></script>
    </body>
</html>
