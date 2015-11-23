<?php
session_start();

require_once './db.php';



$id = isset($_SESSION['id']) ? $_SESSION['id'] : 'unknown';
$db = new db();

if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    $db->writeLog('ATM', 'Token and session check failed for atm.php page user ID: '.$id);
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}

$customer_name = $db->getUserNameSurname($id);
$ATM_array = $db->getATM();
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        require_once './modules/header.php';
        ?>
        <link rel="stylesheet" href="css/branch.css">
        <script src="https://maps.googleapis.com/maps/api/js"></script>
        <script>
            function initialize() {
                var mapCanvas = document.getElementById('map');
                var mapOptions = {
                    center: new google.maps.LatLng(22.317802, 114.176795),
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                var map = new google.maps.Map(mapCanvas, mapOptions);
                var infoWindow = new google.maps.InfoWindow();
                var marker;
<?php
foreach ($ATM_array as $atm) {
    echo 'marker =new google.maps.Marker({
                    position: new google.maps.LatLng('.$atm['latitude'].','.$atm['longitude'].'),
                    map: map,
                    title: "'.$atm['address'].'"
                });
                google.maps.event.addListener(marker, "click", (function (marker) {
                    return function () {
                        infoWindow.setContent(
                                "<div>" +
                                "<h4>'.$atm['address'].'</h4>" +
                                "<p><span><strong>State: </strong></span>'.$atm['state'].'</p>" +    
                                "</div>"
                                );
                        infoWindow.open(map, marker);
                    }
                })(marker));';
    
}
echo '}';
?>
                google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    </head>
    <body>
        <?php
        require_once './modules/logo.php';
        echo '<p id="user_message">Logged As: ' . $customer_name . ' <a href="/index.php?logout=true">Log Out</a></p>';
        require_once './modules/menubar.php';
        ?>
        <div id="background" style="padding: 30px;">
            <h4>Bank ATM Table and Map Location</h4>
            <h5><strong><a href="branch.php?token=<?php echo $token; ?>">Check Branches Table and Location</a></strong></h5>
            <div id="map"></div>
            <h4 class="error"><?php echo $error; ?></h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>
                            Address
                        </td>
                        <td>
                           State
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($ATM_array as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['address'] . '</td>';
                        $class = strcmp($row['state'], 'busy') == 0 ? 'state_busy' : 'state_av';
                        echo '<td class="'.$class.'">' . $row['state'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>

        </div>

        <?php
        require_once './modules/footer.php';
        ?>

        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>

    </body>
</html>

