<?php

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Stock Chart</title>
    <script type="text/javascript" src="//www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});

        function drawVisualization() {
            // Some raw data (not necessarily accurate)
            var data = google.visualization.arrayToDataTable([
			
<?php
	class Record {
		public $date, $adjClose, $ma10, $ma20;
		
		public function __construct($date, $adjClose) {
			$this->date = $date;
			$this->adjClose = $adjClose;
		}
	}
	
	function download() {
		$stock = $_GET['symbol'];
		$sMonth = $_GET['sM'] - 1;
		$sDay = $_GET['sD'];
		$sYear = $_GET['sY'];
		$eMonth = $_GET['eM'] - 1;
		$eDay = $_GET['eD'];
		$eYear = $_GET['eY'];
		
		$url = "http://ichart.finance.yahoo.com/table.csv?s=$stock&d=$eMonth&e=$eDay&f=$eYear&g=d&a=$sMonth&b=$sDay&c=$sYear&ignore=.csv";
		return file_get_contents($url);
	}

	function getData($content) {
		$records = array();

		$arr = explode("\n", $content);
		$numOfCols = sizeof(explode(",", $arr[0]));
		$numOfRows = sizeof($arr) - 1;
		for ($r=0; $r<$numOfRows - 1; $r++) {
			$temp = explode(",", $arr[$numOfRows - $r - 1]);
			if (sizeof($temp) < $numOfCols - 2)
				continue;
			$records[$r] = new Record($temp[0], $temp[$numOfCols -1]);
			$records[$r]->ma10 = calcMA($records, $r, 10);
			$records[$r]->ma20 = calcMA($records, $r, 20);
		}
		return $records;
	}

	function calcMA($records, $i, $days) {
		$ma = 0;
		$count = $days < $i+1? $days:$i+1;
			
		for ($j=0; $j<$count; $j++) 
			$ma += $records[$i - $j]->adjClose;
		$ma /= $count;
		return $ma;
	}

	function listAll($records) {

		echo "['Date', 'Close Price', 'Moving Average 10', 'Moving Average 20']";
		for ($r=0; $r<sizeof($records); $r++) {
			printf(",\n['%s', %f, %f, %f]", 
				$records[$r]->date, 
				$records[$r]->adjClose, 
				$records[$r]->ma10, 
				$records[$r]->ma20);
		}
	}

	$content = download();
	$records = getData($content);
	listAll($records);
?>

			  
			]);
      
			var ac = new google.visualization.ComboChart(document.getElementById('visualization'));
			ac.draw(data, {
				title : 'Stock Market',
				width: 1024,
				height: 768,
				vAxis: {title: "Price"},
				hAxis: {title: "Date"},
				seriesType: "bars",
				series: {1: {type: "line"}, 2: {type: "line"}}
			});
        }
      
      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
	<form method="get" src="stock.php">
	<table>
	<tr>
	<td>Stock symbol:</td>
	<td><input name="symbol" />(9999.HK)</td>
	</tr>
	<tr>
	<td>from: </td>
	<td><input name="sY" size="4"/>(y)<input name="sM" size="2"/>(m)<input name="sD" size="2"/>(d)</td>
	</tr>
	<tr>
	<td>to: </td>
	<td><input name="eY" size="4"/>(y)<input name="eM" size="2"/>(m)<input name="eD" size="2"/>(d)</td>
	<td><input name='Submit' type='submit' value='Submit'></td>
	</tr>
	</table>
	</form>
      
      <?php 
        var_dump($content);
      ?>
    <div id="visualization"></div>
    
  </body>
</html>
​