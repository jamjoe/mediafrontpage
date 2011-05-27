<?php
$wIndex["wTransmission"] = array("name" => "Transmission", "type" => "ajax", "block" => "transmissionwrapper", "headerfunction" => "widgetTransmissionHeader();", "call" => "widgets/wTransmission.php?style=w", "interval" => 10000);

function widgetTransmissionHeader() {
	echo <<< TRANSMISSIONHEADER
		<script type="text/javascript" language="javascript">
		<!--
			function sendArgument(method){
				var xmlhttp=null;
				var url = "widgets/TransmissionComponents/transmission_commands.php?";
				if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}
				else{// code for IE6, IE5
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState==4) {
						if (xmlhttp.status==200 || window.location.href.indexOf("http")==-1) {
							document.getElementById("load").innerHTML = "...";
							updateTransmission();
						} else {
							//alert("An error has occured making the request");
						}
					}
				}
				xmlhttp.open("GET", url+method, true);
				xmlhttp.send(null);
			}
			
			function updateTransmission(){
				var xmlhttp=null;
				var url = "widgets/wTransmission.php?style=w";
				if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}
				else{// code for IE6, IE5
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}				
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						document.getElementById("transmissionwrapper").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET", url, true);
				xmlhttp.send(null);
			}
		-->
		</script>
TRANSMISSIONHEADER;
}

function widgetTransmission(){
	global $transmission_url, $transmission_admin, $transmission_pass;
	$cmdpath = 'widgets/TransmissionComponents/transmission_commands?';
	require_once('TransmissionComponents/transmission_api.php' );
	echo '<div id="transmission_body" width="100%" overflow="hidden">';
	
	try{
		$rpc = new TransmissionRPC($transmission_url);
		$rpc->username = $transmission_admin;
		$rpc->password = $transmission_pass;
		//$rpc->debug = true;

		$session = $rpc->getSession(null)->arguments;
		$current_stats = $session->current_stats;
		$upload_speed = ByteSize2($session->uploadSpeed)."/s";
		$download_speed = ByteSize2($session->downloadSpeed)."/s";
		$active_torrents = $session->activeTorrentCount;
		$paused_torrents = $session->pausedTorrentCount;
		$total_torrents = $session->torrentCount;
		$downloaded_bytes = ByteSize2($current_stats->downloadedBytes);
		$uploaded_bytes = ByteSize2($current_stats->uploadedBytes);
		$dl_speed_limit = $rpc->getSessionInfo(null)->arguments->speed_limit_down;
		$ul_speed_limit = $rpc->getSessionInfo(null)->arguments->speed_limit_up;
		
		echo "<div style='float:right;padding-right:2px;'><form target='nothing' action='".$cmdpath."' method='get'>";
		echo "<input type='text'  class='btnDown' value='".$dl_speed_limit."' id='dllimit' name='dllimit' size='2' />";
		echo "<input type='text'  class='btnUp' value='".$ul_speed_limit."' id='ullimit' name='ullimit' size='2' />";
		echo "<input type='submit' style='position: absolute; left: -9999px'/>";
		echo "</form></div>";
		if($active_torrents!=0){
			echo "<a href='#' target='nothing' onclick=\"sendArgument('stop=all');\">Downloading</a>";
			echo "<p>D: ".$download_speed." - U: ".$upload_speed."</p>";
		}
		else {
			echo "<a href='#' target='nothing' onclick=\"sendArgument('start=all');\">Paused</a>";
		}
		echo "<div id='load'></div>";

		$torrents = $rpc->get();
		if($torrents->result == 'success' && (!empty($torrents->arguments->torrents))){
			//print_r($torrents);

			echo "<table border=\"0\" width='100%' style='table-layout:fixed;' cellspacing='0' cellpadding='0'><tr>";
			echo "<th style='width:30%;'></th>";
			echo "<th width='50%'></th>";
			echo "<th></th>";
			echo "</tr>";
			foreach ($torrents->arguments->torrents as $item){

				$name		=	$item->name;
				$status		=	$item->status;
				$size		=	ByteSize2($item->totalSize);
				$id   		= 	$item->id;
				$dl_dir  	= 	$item->downloadDir;
				$dl_rate 	= 	ByteSize2($item->rateDownload).'/s';
				$ul_rate 	= 	ByteSize2($item->rateUpload).'/s';
				$ratio  	=	$item->uploadRatio;
				$progress	=	($item->percentDone*100).'%';
				
				$eta 		= 	$item->eta;
				$eta_hr		=	floor($eta/60/60);
				$eta_min 	= 	($eta<60)?$eta:floor($eta/60-$eta_hr*60);
				
				$popup = "<p>Name: ".$name."</p>";
				$popup .= "<p>Ratio: ".$ratio."</p>";
				$popup .= "<p>Download Directory: ".$dl_dir."</p>";
				$popup .= "<p>ID: ".$id."</p>";
				$popup .= "<p>DL Speed: ".$dl_rate."</p>";
				$popup .= "<p>UL Speed: ".$ul_rate."</p>";
				$popup .= "<p>ETA: ";
				$popup .= ($eta>0)?$eta_hr.":".$eta_min:"Unknown";
				$popup .= "</p>";
				
				$colour = "";
				$playpausebtn = "stop=";
				switch($status){
				case 1:
					$status = "Waiting in queue to check files";
					$colour = "#FFFC17"; //Yellow1
					break;
				case 2:
					$status = "Checking Files";
					$colour = "#FFFC17"; //Yellow1
					break;
				case 4:
					$status = "Downloading";
					$colour = "#347C17"; //Green4 #FFFC17
					break;
				case 8:
					$status = "Seeding";
					$colour = "#347C17"; //Green4
					break;
				case 16:
					$status = "Paused";
					$colour = "#FF0000"; //Red
					$playpausebtn = "start=";
					break;
				}

				$popup .= "<p>Status: ".$status."</p>";

				

				echo "<tr>";
				echo "<td onMouseOver=\"ShowPopupBox('".$popup."');\" onMouseOut=\"HidePopupBox();\"><font color=".$colour."><div style='text-overflow:ellipsis;
overflow:hidden; white-space: nowrap;'>".$name."</div></font></td>";
				echo "<td><div class=\"queueitem\">";
				echo "\t\t\t\t<div class=\"progressbar\">";
				echo "\t\t\t\t\t<div class=\"progress\" style=\"width:".$progress."\"></div>";
				echo "\t\t\t\t\t<div class=\"progresslabel\" style='text-align: center;'>".$progress." of ".$size."</div>";
				echo "\t\t\t\t</div><!-- .progressbar -->";
				echo "\t\t\t</div><!-- .queueitem -->";
				echo "</td>";
				echo "<td><a href='#' target='nothing' onclick=\"sendArgument('".$playpausebtn.$id."');\"><img src='media/btnPlayPause.png' width='20px'/></a>";
				echo "<a href='#' target='nothing' onclick=\"sendArgument('remove=".$id."');\"><img src='media/btnQueueDelete.png' width='20px'/></a></td>";
				echo "</tr>";
			}
			echo "<div class='clear-float'></div>";
			echo "</table>\n";
		}
	} catch(Exception $e){
		echo "Program is probably closed or <a href='".$transmission_url."'><font color='red'>THIS</font></a> is not the right address.";
		//if there is need to see the errors, just uncomment the line below
		//echo $e;
	}
	echo "<div style='clear:both;'></div>";
	echo '</div>';
}
function ByteSize2($bytes)
{
	$size = $bytes / 1000;
	if($size < 1000)
	{
		$size = number_format($size, 2);
		$size .= ' KB';
	}
	else
	{
		if($size / 1000 < 1000)
		{
			$size = number_format($size / 1000, 2);
			$size .= ' MB';
		}
		else if ($size / 1000 / 1000 < 1000)
			{
				$size = number_format($size / 1000 / 1000, 2);
				$size .= ' GB';
			}

		else if ($size / 1000 / 1000 / 1000 < 1000)
			{
				$size = number_format($size / 1000 / 1000/ 1000, 2);
				$size .= ' TB';
			}
	}
	return $size;
}

if(!empty($_GET['style']) && ($_GET['style'] == "w")) {
	require_once "../config.php";
	if($_GET['style'] == "w") {
?>
<html>
	<head>
		<title>Media Front Page - Transmission</title>
		<link rel='stylesheet' type='text/css' href='css/front.css'>
	</head>
	<body>
<?php
		widgetTransmission();
?>
	</body>
</html>
<?php
	} else {
		widgetTransmission();
	}
}
?>
