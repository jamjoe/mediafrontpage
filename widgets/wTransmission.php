<?php
$wIndex["wTransmission"] = array("name" => "Transmission", "type" => "ajax", "block" => "transmissionwrapper", "headerfunction" => "widgetTransmissionHeader();", "call" => "widgets/wTransmission.php?style=w", "interval" => 5000);
function widgetTransmissionHeader() {
	echo <<< TRANSMISSIONHEADER
		<script type="text/javascript" language="javascript">
		<!--
			function cmdTransmission(requesturl) {
				
				//alert(requesturl);
				
				var cmdTransmissionRequest = new ajaxRequest();
				cmdTransmissionRequest.open("GET", requesturl, true);
				cmdTransmissionRequest.onreadystatechange = function() {
					if (cmdTransmissionRequest.readyState==4) {
						if (cmdTransmissionRequest.status==200 || window.location.href.indexOf("http")==-1) {
							document.getElementById("transmissionwrapper").innerHTML = cmdTransmissionRequest.responseText;
						} else {
							//alert("An error has occurred making the request");
						}
					}
				}
				cmdTransmissionRequest.send(null);
			}
		-->
		</script>
TRANSMISSIONHEADER;
}

function widgetTransmission(){ 
	global $transmission_url, $transmission_admin, $transmission_pass;

require_once('transmission_api.php' );
    echo '<div id="transmission_body" width="100%" overflow="hidden">';
$rpc = new TransmissionRPC($transmission_url);

$rpc->username = $transmission_admin;
$rpc->password = $transmission_pass;
//$rpc->debug = true;


if($_GET['stop']=='all'){
	$rpc->stop(array());
	echo 'Stopped';
} 
if($_GET['start']=='all'){
	$rpc->start(array());
	echo 'Resumed';
} 

$session = $rpc->getSession(null)->arguments;
$current_stats = $session->current_stats;
$upload_speed = "@".ByteSize2($session->uploadSpeed)."/s";
$download_speed = "@".ByteSize2($session->downloadSpeed)."/s";
$active_torrents = $session->activeTorrentCount;
$paused_torrents = $session->pausedTorrentCount;
$total_torrents = $session->torrentCount;
$downloaded_bytes = ByteSize2($current_stats->downloadedBytes);
$uploaded_bytes = ByteSize2($current_stats->uploadedBytes);

	
	echo ($active_torrents!=0)?"<a href='?stop=all'>Downloading</a>":"<a href='?start=all'>PAUSED</a>";


	echo "<table border=\"1\" width='100%'>\n";
	echo "\t<col><col><col><col><col>\n";
	echo "\t<tr>\n";
/*	echo "\t\t<th>Total Torrents</th>\n";
	echo "\t\t<th>Active</th>\n";
	echo "\t\t<th>Paused</th>\n";
*/	echo "\t\t<th>Downloaded</th>\n";
	echo "\t\t<th>Uploaded</th>\n";
	echo "\t</tr>\n";
	echo "\t<tr>\n";
/*	echo "\t\t<td>".$total_torrents."</td>\n";
	echo "\t\t<td>".$active_torrents."</td>\n";
	echo "\t\t<td>".$paused_torrents."</td>\n";
*/	echo "\t\t<td>".$downloaded_bytes.$download_speed."</td>\n";
	echo "\t\t<td>".$uploaded_bytes.$upload_speed."</td>\n";
	echo "\t</tr>\n";
	echo "</table>\n";	

$torrents = $rpc->get();
if($torrents->result == 'success' && (!empty($torrents->arguments->torrents))){
	//print_r($torrents);

	echo "<table border=\"1\"><tr>";
	echo "<th>ID</th>";
	echo "<th>Name</th>";
	echo "<th>Progress</th>";
	echo "<th>Status</th>";
	echo "<th>Size</th>";
	echo "<th>DL Rate</th>";
	echo "<th>UP Rate</th>";
	echo "<th>Ratio</th>";
	echo "<th>Resume</th>";
	echo "<th>Pause</th>";
	echo "<th>Delete</th>";
	echo "<th>DL Directory</th></tr>";
	foreach ($torrents->arguments->torrents as $item){
		
		$name		= 	$item->name;
		$status		= 	$item->status;
		$size		= 	ByteSize2($item->totalSize);
		$id			=	$item->id;
		$dl_dir		=	$item->downloadDir;
		$dl_rate	=	ByteSize2($item->rateDownload).'/s';
		$ul_rate	=	ByteSize2($item->rateUpload).'/s';
		$ratio		=	$item->uploadRatio;
		$progress	=	($item->percentDone*100).'%';

		if($_GET['start']==$id){
			$rpc->start($id);
		}

		if($_GET['stop']==$id){
			$rpc->stop($id);
		}
		if($_GET['remove']==$id){
			$rpc->remove($id);
		}

		switch($status){
			case 1:
				$status = "Waiting in queue to check files";
				break;
			case 2:
				$status = "Checking Files";
				break;
			case 4:
				$status = "Downloading";
				break;
			case 8:
				$status = "Seeding";
				break;
			case 16:
				$status = "Paused";
				break;
		}

		
		echo "<tr>";
		echo "<td>".$id."</td>";
		echo "<td>".$name."</td>";
		echo "<td><div class=\"queueitem\">\n";
		echo "\t\t\t\t<div class=\"progressbar\">\n";
		echo "\t\t\t\t\t<div class=\"progress\" style=\"width:".$progress."\"></div>\n";
		echo "\t\t\t\t\t<div class=\"progresslabel\">".$progress."</div>\n";
		echo "\t\t\t\t</div><!-- .progressbar -->\n";
		echo "\t\t\t</div><!-- .queueitem -->\n";
		echo "</td>\n";
		echo "<td>".$status."</td>";
		echo "<td>".$size."</td>";
		echo "<td>".$dl_rate."</td>";
		echo "<td>".$ul_rate."</td>";
		echo "<td>".$ratio."</td>";
		echo "<td><a href='?start=".$id."'><img src='media/btnPlayPause.png' width='20px'/></a></td>";
		echo "<td><a href='?stop=".$id."'><img src='media/btnPlayPause.png' width='20px'/></a></td>";
		echo "<td><a href='?remove=".$id."'><img src='media/btnQueueDelete.png' width='20px'/></a></td>";
		echo "<td>".$dl_dir."</td>";
		echo "</tr>";
		}	
		echo "</table>\n";
	}
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
		<title>Media Front Page - SABnzbd Status</title>
		<link rel='stylesheet' type='text/css' href='css/front.css'>
	</head>
	<body>
<?php
		widgetTransmission($count);
?>
	</body>
</html>
<?php
	} else {
		widgetTransmission($count);
	}
}
?>
