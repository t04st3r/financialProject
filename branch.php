<?php
session_start();

require_once './db.php';



if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}

$id = $_SESSION['id'];
$db = new db();
$customer_name = $db->getUserNameSurname($id);
$branches_array = $db->getBranches();
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
//sorry for that, I know it is ugly but had no time to grab values with jQuery :'( 
foreach ($branches_array as $branch) {
    echo 'marker =new google.maps.Marker({
                    position: new google.maps.LatLng('.$branch['latitude'].','.$branch['longitude'].'),
                    map: map,
                    title: "'.$branch['city'].'"
                });
                google.maps.event.addListener(marker, "click", (function (marker) {
                    return function () {
                        infoWindow.setContent(
                                "<div>" +
                                "<h4>'.$branch['city'].'</h4>" +
                                "<p><span>Address: </span>'.$branch['address'].'</p>" +
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
            <h4>Bank Branches Table and Location</h4>
            <div id="map"></div>
            <h4 class="error"><?php echo $error; ?></h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>
                            Address
                        </td>
                        <td>
                            City
                        </td>
                        <td>
                            Phone Number
                        </td>
                        <td>
                            Opening Time
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($branches_array as $row) {
                        echo '<tr>';
                        echo '<td>' . $row['address'] . '</td>';
                        echo '<td>' . $row['city'] . '</td>';
                        echo '<td>' . $row['phone'] . '</td>';
                        echo '<td>' . $row['open_time'] . '</td>';
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

