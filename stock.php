<?php

class Record {

    public $date, $adjClose, $ma10, $ma20;

    public function __construct($date, $adjClose) {
        $this->date = $date;
        $this->adjClose = $adjClose;
    }

}

session_start();

require_once './db.php';



if (!isset($_SESSION['token']) || !isset($_SESSION['id']) || $_GET['token'] != $_SESSION['token']) {
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '?err=auth');
}


$stock = isset($_GET['popStocks']) && strcmp($_GET['popStocks'], '') != 0 ? filter_input(INPUT_GET, 'popStocks', FILTER_SANITIZE_SPECIAL_CHARS) : '';

if (isset($_GET['ticket']) && strcmp($_GET['ticket'], '') != 0 && strcmp($stock, '') == 0) {
    $stock = filter_input(INPUT_GET, 'ticket', FILTER_SANITIZE_SPECIAL_CHARS);
}
$startDate = isset($_GET['startDate']) ? filter_input(INPUT_GET, 'startDate', FILTER_SANITIZE_SPECIAL_CHARS) : '';
$endDate = $endDate = isset($_GET['endDate']) ? filter_input(INPUT_GET, 'endDate', FILTER_SANITIZE_SPECIAL_CHARS) : '';


$array_start_date = explode('-', $startDate);
$array_end_date = explode('-', $endDate);

$sMonth = $array_start_date[1] - 1;
$sDay = $array_start_date[2];
$sYear = $array_start_date[0];
$eMonth = $array_end_date[1] - 1;
$eDay = $array_start_date[2];
$eYear = $array_start_date[0];

$url = "http://ichart.finance.yahoo.com/table.csv?s=$stock&d=$eMonth&e=$eDay&f=$eYear&g=d&a=$sMonth&b=$sDay&c=$sYear&ignore=.csv";
$content = file_get_contents($url);

//data not received or empty response
$data_response = !is_bool($content) && strlen($content) > 42 ? true : false;

function getData($content) {
    $records = array();

    $arr = explode("\n", $content);
    $numOfCols = sizeof(explode(",", $arr[0]));
    $numOfRows = sizeof($arr) - 1;
    for ($r = 0; $r < $numOfRows - 1; $r++) {
        $temp = explode(",", $arr[$numOfRows - $r - 1]);
        if (sizeof($temp) < $numOfCols - 2) {
            continue;
        }
        $records[$r] = new Record($temp[0], $temp[$numOfCols - 1]);
        $records[$r]->ma10 = calcMA($records, $r, 10);
        $records[$r]->ma20 = calcMA($records, $r, 20);
    }
    return $records;
}

function calcMA($records, $i, $days) {
    $ma = 0;
    $count = $days < $i + 1 ? $days : $i + 1;

    for ($j = 0; $j < $count; $j++) {
        $ma += $records[$i - $j]->adjClose;
    }
    $ma /= $count;
    return $ma;
}

function listAll($records) {

    echo "['Date', 'Close Price', 'Moving Average 10d', 'Moving Average 20d']";
    for ($r = 0; $r < sizeof($records); $r++) {
        printf(",\n['%s', %f, %f, %f]", $records[$r]->date, $records[$r]->adjClose, $records[$r]->ma10, $records[$r]->ma20);
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
<?php
require_once './modules/header.php';
?>
        <link rel="stylesheet" href="css/stock.css">
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <script type="text/javascript" src="//www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load('visualization', '1', {packages: ['corechart']});

            function drawVisualization() {
                // Some raw data (not necessarily accurate)
                var data = google.visualization.arrayToDataTable([
<?php
if ($data_response) {
    $records = getData($content);
    listAll($records);
    $div_style = '';
}else{
    $div_style = 'style="display:none"';
}
?>


                ]);

                var ac = new google.visualization.ComboChart(document.getElementById('visualization'));
                ac.draw(data, {
                    title: 'Stock Price <?php echo $stock." Ticket"?>',
                    width: 1024,
                    height: 768,
                    vAxis: {title: "Price $HK"},
                    hAxis: {title: "Date"},
                    seriesType: "bars",
                    series: {1: {type: "line"}, 2: {type: "line"}}
                });
            }

            google.setOnLoadCallback(drawVisualization);
        </script>

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
            <h4 class="search_title"><?php 
                if(strcmp($stock, '') == 0){
                    echo 'Select a stock or insert a stock ticket to visualize price trends';
                }
                if($data_response){
                    echo 'Hong Kong Stock Market Price Trends';
                }
            ?></h4>
            <form class="form-inline" method="GET">
                <div class="form-group">
                    <label for="popStocks">Stocks</label>
                    <select name="popStocks" class="form-control" id="selectAccount">
                        <option></option>
                        <option value="0001.HK">CKH HOLDINGS</option>
                        <option value="0002.HK">CLP Holdings Ltd.</option>
                        <option value="0004.HK">The Wharf Limited</option>
                        <option value="0005.HK">HSBC Holdings plc</option>
                        <option value="0006.HK">Power Assets Holdings Limited</option>
                        <option value="0011.HK">Hang Seng Bank Limited</option>
                        <option value="0016.HK">Sun Hung Kai Properties Limited</option>
                        <option value="0019.HK">Swire Pacific Limited</option>
                        <option value="0023.HK">The Bank of East Asia, Limited</option>
                        <option value="0027.HK">Galaxy Entertainment Group Limited</option>
                        <option value="0066.HK">MTR Corporation Limited</option>
                        <option value="0083.HK">Sino Land Company Limited</option>
                        <option value="0101.HK">HANG LUNG PPT</option>
                        <option value="0135.HK">Kunlun Energy Company Limited</option>
                        <option value="0151.HK">Want Want China Holdings Ltd.</option>
                        <option value="0267.HK">CITIC Limited</option>
                        <option value="0291.HK">China Resources Beer Company Limited</option>
                        <option value="0293.HK">Cathay Pacific Airways Limited</option>
                        <option value="0322.HK">Tingyi Cayman Islands Holding Corp.</option>
                        
                    </select>
                    <div class="form-group">
                        <label for="startAmount">Insert Ticket</label>
                        <input name="ticket" type="text" class="form-control" id="startAmount" placeholder="Insert Ticket">
                    </div>
                </div>
                <div class="form-group">
<?php $date = date('Y-m-d'); ?>
                    <label for="startDate">Start Date</label>
                    <input name="startDate" type="date" class="form-control" id="startDate" max="<?php echo $date; ?>">
                </div>
                <div class="form-group">
                    <label for="endDate">End Date</label>
                    <input name="endDate" type="date" class="form-control" id="endDate" max="<?php echo $date; ?>">
                </div>
                <input type="hidden" value="<?php echo $token ?>" name="token">
                <button type="submit" class="btn btn-default">Search</button>
            </form>
            
            <div id="visualization" <?php echo $div_style; ?>></div>

        </div>

<?php
require_once './modules/footer.php';
?>

        <script src="js/jquery-2.1.4.min.js"></script>   
        <script src="js/bootstrap.min.js"></script>

    </body>
</html>