<?php
$wIndex["wjDownloader"]  = array("name" => "jDownloader", "type" => "ajax", "block" => "jdownloaderwrapper", "call" => "widgets/wjDownloader.php?style=w", "interval" => 2000);

function widgetjDownloader(){
	global $jd_url;
	$jd = $jd_url;

	$speed   = file_get_contents($jd."get/speed");
	$status  = file_get_contents($jd."get/downloadstatus");

	$speedLimit = file_get_contents($jd."get/speedlimit");

	$dlCount  = file_get_contents($jd."get/downloads/currentcount");
	$dlList  =  new SimpleXMLElement(file_get_contents($jd."get/downloads/currentlist"));

	// echo "<pre>";
	// print_r($dlList);
	// echo "</pre>";

	$finished  = file_get_contents($jd."get/downloads/finishedlist");
	$finishedCount = file_get_contents($jd."get/downloads/finishedcount");

	$allCount = file_get_contents($jd."get/downloads/allcount");
	$allList = file_get_contents($jd."get/downloads/alllist");

	$dllimi = file_get_contents($jd."get/speedlimit");
	$dllimit = file_get_contents($jd."get/speedlimit");

	$action="";

	switch($status){
	case 'RUNNING':
		$status="Downloading @".$speed." kb/s";
		$action = $jd."action/stop";
		break;
	case 'STOPPING':
		$status="Stopping";
		break;
	case 'NOT_RUNNING':
		$status="Stopped";
		$action = $jd."action/start";
		break;
	}

	echo "<a href='".$action."' target='nothing'>".$status."</a>";
	echo "<p>Speed Limit: ".$speedLimit;
	echo " kb/s</p>";
		echo "<table border=\"0\" width='100%' style='table-layout:fixed;' cellspacing='0' cellpadding='0'><tr>";
		echo "<th style='width:30%;'></th>";
		echo "<th width='50%'></th>";
		echo "<th></th>";
		echo "</tr>";

		foreach($dlList as $item){
			$eta    = $item->attributes()->package_ETA;
			$links_progress = $item->attributes()->package_linksinprogress;
			$links_total = $item->attributes()->package_linkstotal;
			$downloaded  = $item->attributes()->package_loaded;
			$total_size  = $item->attributes()->package_size;
			$name     = $item->attributes()->package_name;
			$percent  = $item->attributes()->package_percent;
			$speed    = $item->attributes()->package_speed;
			$to_do    = $item->attributes()->package_todo;
			if($links_progress!=0){
				$hoster   = $item->file->attributes()->file_hoster;
				$to_do    = $item->attributes()->package_todo;
			}
			$color = "red";


			$popup  = "";
			$popup .= "<p>".$links_progress." of ".$links_total."</p>";
			$popup .= "<p>Progress: ".$percent."% (".$downloaded."/".$total_size.")</p>";
			if($links_progress!=0){
				$popup .= "<p>Hoster: ".$hoster."</p>";
				$color = "green";
			}

			echo "<tr>";
			echo "<td onMouseOver=\"ShowPopupBox('".$popup."');\" onMouseOut=\"HidePopupBox();\"><font color=".$color."><div style='text-overflow:ellipsis;overflow:hidden; white-space: nowrap;'>".$name."</div></font></td>";
			echo "<td><div class=\"queueitem\">";
			echo "\t\t\t\t<div class=\"progressbar\">";
			echo "\t\t\t\t\t<div class=\"progress\" style=\"width:".$percent."\"></div>";
			echo "\t\t\t\t\t<div class=\"progresslabel\" style='text-align: center;'>".$to_do." left @ ".$speed." - ".$eta."</div>";
			echo "\t\t\t\t</div><!-- .progressbar -->";
			echo "\t\t\t</div><!-- .queueitem -->";
			echo "</td>";
			echo "<td><a href='#' target='nothing' onclick=\"sendArgument('".$playpausebtn.$id."');\"><img src='media/btnPlayPause.png' width='20px'/></a>";
			echo "<a href='#' target='nothing' onclick=\"sendArgument('remove=".$id."');\"><img src='media/btnQueueDelete.png' width='20px'/></a></td>";
			echo "</tr>";
		}
		echo "</table>\n";
	if(intval($finishedCount)!=0){
		echo "<p>Finished Count: ".$finishedCount."</p>";
		echo "<p>Finished: ".$finished."</p>";
	}
/*
	echo "<p>All Count: ".$allList."</p>";
	echo "<p>All list: ".$allList."</p>";
	echo "<p>Finished: ".$finished."</p>";
*/
}
/*
JDRemoteControl 9568

Usage:

1)Replace %X% with your value
Sample: /action/save/container/C:\backup.dlc
2)Replace (true|false) with true or false

Get Values:

/get/speed						Get current Speed
/get/ip							Get IP
/get/randomip					Answers with Random IP as replacement for real IP-Check
/get/config	Get 				Config
/get/version					Get Version
/get/rcversion					Get RemoteControl Version
/get/speedlimit					Get current Speedlimit
/get/isreconnect				Get If Reconnect
/get/downloadstatus				Get Downloadstatus
								Values: RUNNING, NOT_RUNNING, STOPPING
/get/downloads/currentcount		Get amount of current downloads
/get/downloads/currentlist		Get Current Downloads List (XML)
/get/downloads/allcount			Get amount of downloads in list
/get/downloads/alllist			Get list of downloads in list (XML)
/get/downloads/finishedcount	Get amount of finished Downloads
/get/downloads/finishedlist		Get finished Downloads List (XML)

Actions:

/action/start					Start DLs
/action/pause					Pause DLs
/action/stop					Stop DLs
/action/toggle					Toggle DLs
/action/update/force(0|1)/		Do Webupdate
									force1 activates auto-restart if update is possible
/action/reconnect				Do Reconnect
/action/restart					Restart JD
/action/shutdown				Shutdown JD
/action/set/download/limit/%X%	Set Downloadspeedlimit %X%
/action/set/download/max/%X%	Set max sim. Downloads %X%

/action/add/links/grabber(0|1)/start(0|1)/%X%	Add Links %X% to Grabber
												Optional:
												grabber(0|1): Hide/Show LinkGrabber
												grabber(0|1)/start(0|1): Hide/Show LinkGrabber and start/don't start downloads afterwards

												Sample:
												/action/add/links/grabber0/start1/http://tinyurl.com/6o73eq http://tinyurl.com/4khvhn
												Don't forget Space between Links!

/action/add/container/grabber(0|1)/start(0|1)/%X%	Add Container %X%
													Optional:
													grabber(0|1): Hide/Show LinkGrabber
													grabber(0|1)/start(0|1): Hide/Show LinkGrabber and start/don't start downloads afterwards

													Sample:
													/action/add/container/grabber0/start1/C:\container.dlc

/action/save/container/%X%		Save DLC-Container with all Links to %X%
								Sample see /action/add/container/%X%

/action/set/reconnectenabled/(true|false)	Set Reconnect enabled or not
/action/set/premiumenabled/(true|false)		Set Use Premium enabled or not
*/




if(!empty($_GET['style']) && ($_GET['style'] == "w")) {
	require_once "../config.php";
	if($_GET['style'] == "w") {
?>
<html>
	<head>
		<title>Media Front Page - jDownloader</title>
		<link rel='stylesheet' type='text/css' href='css/front.css'>
	</head>
	<body>
					<iframe name="nothing" height="0" width="0" style="visibility:hidden;display:none;"></iframe>
<?php
		widgetjDownloader();
?>
	</body>
</html>
<?php
	} else {
		widgetjDownloader();
	}
}
?>
