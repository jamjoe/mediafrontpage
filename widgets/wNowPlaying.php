<?php
$wdgtNowPlayingAjax = array("type" => "ajax", "block" => "nowplayingwrapper", "call" => "widgets/wNowPlaying.php?ajax=w", "interval" => 50000);
$wdgtNowPlayingControls = array("type" => "inline", "function" => "widgetNowPlayingControls();", "headerfunction" => "widgetNowPlayingHeader();");
$wdgtNowPlaying = array("name" => "Now Playing", "type" => "mixed", "parts" => array($wdgtNowPlayingAjax, $wdgtNowPlayingControls));
$wIndex["wNowPlaying"] = $wdgtNowPlaying;

function widgetNowPlayingControls($baseurl = "") {
	echo "<div id=\"nowplaying-controls\" class=\"controls\">\n";
	echo "\t".anchorControlButton($baseurl, 'SkipPrevious', 'btnSkipBackward.png', 'Skip Back')."\n";
	echo "\t".anchorControlButton($baseurl, 'PlayPause', 'btnPlayPause.png', 'Play/Pause')."\n";
	echo "\t".anchorControlButton($baseurl, 'Stop', 'btnStop.png')."\n";
	echo "\t".anchorControlButton($baseurl, 'SkipNext', 'btnSkipForward.png', 'Skip Next')."\n";
	echo "\t".anchorControlButton($baseurl, 'ShowPlaylist', 'btnPlayList.png')."\n";
	echo "</div>\n";
	echo "<div class=\"clear-float\"></div>\n";
	echo "<div id=\"nowplaying-list\"></div>\n";
}
function widgetNowPlayingHeader() {
	echo <<< NOWPLAYINGHEADER
		<script type="text/javascript" language="javascript">
		<!--
			function cmdNowPlaying(cmd) {
				var cmdXbmcPlayingRequest = new ajaxRequest();
				cmdXbmcPlayingRequest.open("GET", "widgets/wNowPlaying.php?ajax=c&command="+cmd, true);
					cmdXbmcPlayingRequest.onreadystatechange = function() {
						if (cmdXbmcPlayingRequest.readyState==4) {
							if (cmdXbmcPlayingRequest.status==200 || window.location.href.indexOf("http")==-1) {
								document.getElementById("nowplaying-list").innerHTML=cmdXbmcPlayingRequest.responseText;
							} else {
								alert("An error has occured making the request");
							}
						}
					}

				cmdXbmcPlayingRequest.send(null);
			}
		-->
		</script>

NOWPLAYINGHEADER;
}
?>
<?php
function anchorControlButton($baseurl, $cmd, $img = "", $label = "") {
	if(empty($label)) {
		$label = $cmd;
	}
	if(!empty($_GET['style']) && ($_GET['style'] == "m")) {
		$mediadir = "../media";
		$anchorlink = "href=\"".$baseurl."?style=m";
		$anchorlink .= (!empty($_GET['w']) ? "&w=".$_GET['w'] : "");
		$anchorlink .= "&cmd=".$cmd."\"";
	} else {
		$mediadir = "media";
		$anchorlink = "onclick=\"cmdNowPlaying('".$cmd."');\" href=\"#\"";
	}
	$anchorlabel = (!empty($img) ? "<img src=\"".$mediadir."/".$img."\" alt=\"".$label."\"/>" : $label);
	return "<a class=\"controlbutton\" ".$anchorlink.">".$anchorlabel."</a>";
}
function displayNowPlaying($baseurl = "") {
	global $xbmcimgpath;
	
	if(!empty($_GET['style']) && ($_GET['style'] == 'm') && !empty($_GET['cmd'])) {
		processCommand($_GET['cmd']);
	}
	
	echo "<div id=\"nowplaying\">\n";

	//json rpc call procedure
	$results = jsonmethodcall("Player.GetActivePlayers");

	//video Player
	if (($results['result']['video']) == 1) {
		$jsonVersion = jsonmethodcall("JSONRPC.Version"); //pull the JSON version # from XBMC
		//get playlist items
		if($jsonVersion['result']['version'] == '2')
		{ // For the old version
			$results = jsonmethodcall("VideoPlaylist.GetItems"); //Pull the current "playlist"
			if(!empty($results['result']['items'])) { $items = $results['result']['items'][0]; } //If there's anything there, get the current item
			//The older interface doesn't explicitly return a "type"
			if(isset($items['episode'])) //However, all TV shows will have an episode #, so if this is set, we're watching a TV show
			{
				if(!empty($items['showtitle'])) //We begin building the first line with the show's title
				{
					$line1=$items['showtitle'];
				}
				if(!empty($items['season']))
				{
					$line1=$line1." - S".sprintf("%02d", $items['season']);
				}
				if(empty($items['season'])) //There's a bug with the interface where if you're watching a "special" episode, the season # is 0, but nothing is returned
				{
					$line1=$line1." - S00";
				}
				if(!empty($items['episode']))
				{
					$line1=$line1."E".sprintf("%02d", $items['episode']);
				}
				if(!empty($items['title']))
				{
					$line2='"'.$items['title'].'"';
				}
				if(empty($items['plot']))
				{
					$line3="Plot Summary Unavailable";
					$plot="Plot Summary Unavailable";
				}
				if(!empty($items['plot']))
				{
					$line3=$items['plot'];
					$plot=$items['plot'];
				}
			}
			if(!isset($items['episode'])) //If the episode # is not set, we can assume its a movie and go from there
			{
				if(!empty($items['title']))
				{
					$line1=$items['title'];
				}
				if(!empty($items['year']))
				{
					$line1=$line1.' ('.$items['year'].')';
				}
				if(!empty($items['director']))
				{
					$line1=$line1." | Directed by ".$items['director'];
				}
				if(empty($line1)) //For some reason, with the old interface if you launch a video from one of the widgets, basically NONE of the metadata is populated
				// As a result we can't do any of the normal nice stuff.
				{ // In this lame case, we can fall back to the file name
					$line1=substr($items['file'],strrpos($items['file'],'/'));
				}
				$line2 =''; // set this variable so that PHP won't error if it remains unset
				if(!empty($items['tagline']))
				{
					$line2=$items['tagline'];
				}
				if(empty($items['plot']))
				{
					$line3="Plot Summary Unavailable";
					$plot="Plot Summary Unavailable";
				}
				if(!empty($items['plot']))
				{
					$line3=$items['plot'];
					$plot=$items['plot'];
				}
			}
		}
		if($jsonVersion['result']['version'] == '3') //Handle the new interface
		{
			$results = jsonmethodcall("VideoPlaylistV3.GetItems"); //Pull the current "playlist"
			if(!empty($results['result']['items'])) { $items = $results['result']['items'][0]; } //If there's anything there, get the current item
			if ($items['type']=='episode')
			{
				//its a TV show!
				if(!empty($items['showtitle']))
				{
					$line1=$items['showtitle'];
				}
				if(!empty($items['season']))
				{
					$line1=$line1." - S".sprintf("%02d", $items['season']);
				}
				if(!empty($items['episode']))
				{
					$line1=$line1."E".sprintf("%02d", $items['episode']);
				}
				if(!empty($items['title']))
				{
					$line2='"'.$items['title'].'"';
				}

				if(empty($items['plot']))
				{
					$line3="Plot Summary Unavailable";
					$plot="Plot Summary Unavailable";
				}
				if(!empty($items['plot']))
				{
					$line3=$items['plot'];
					$plot=$items['plot'];
				}

			}
			else if ($items['type']=='movie')
			{
			    //it's a movie
				if(!empty($items['title']))
				{
					$line1=$items['title'];
				}
				if(!empty($items['year']))
				{
					$line1=$line1.' ('.$items['year'].')';
				}
				if(!empty($items['director']))
				{
					$line1=$line1." | Directed by ".$items['director'];
				}
				if(!empty($items['tagline']))
				{
					$line2=$items['tagline'];
				}
				if(empty($items['plot']))
				{
					$line3="Plot Summary Unavailable";
					$plot="Plot Summary Unavailable";
				}
				if(!empty($items['plot']))
				{
					$line3=$items['plot'];
					$plot=$items['plot'];
				}
			}
		}
			if (!empty($items['thumbnail'])) {
				$thumb = $items['thumbnail'];
			} else {
				if(!empty($items['fanart'])){ $thumb = $items['fanart']; }
			}
			if(strlen($thumb) > 0) {
				echo "\t<div id=\"thumbblock\" class=\"thumbblockvideo\">\n";
				if(!empty($baseurl)) {
					echo "\t\t<img src=\"".$xbmcimgpath.$thumb."\" alt=\"".htmlentities($plot, ENT_QUOTES)."\" />";
				} else {
					echo "\t\t <b>".$line1."</b><a href=\"".$xbmcimgpath.$thumb."\" class=\"highslide\" onclick=\"return hs.expand(this)\">\n";
					echo "\t\t\t<img src=\"".$xbmcimgpath.$thumb."\" title=\"Click to enlarge\" alt=\"".htmlentities($plot, ENT_QUOTES)."\" />";
					echo "\t\t</a>\n";
				}
				echo "\t</div>\n";
			}
			echo "\t\t<p><i>".$line2."</i></p>\n";
			echo "\t\t<p>".$line3."</p>\n";
		//progress time
		$results = jsonmethodcall("VideoPlayer.GetTime");
		$time = $results['result']['time']; // Current time (in seconds)
		$total = $results['result']['total']; // Total time (in seconds)
		$timeFormatted = ''; //Instantiate the "timeFormatted" variable so we don't get errors
		//The way time is formatted and returned is different in the old and new JSON interfaces, so we fork here to handle each case.
		if($jsonVersion['result']['version'] == '2')
		{ 
			$timeFormatted = formattimes($time, $total);
			echo "$timeFormatted</p>\n";
		}
		if($jsonVersion['result']['version'] == '3') //For the newer version results are returned in an array
		{
			if($time['hours']!=0) { $timeFormatted = $time['hours'].":"; } //If there's actually any "hours" value, we format and add them to the string
			$timeFormatted = "\t\t<p>".$timeFormatted.sprintf("%02d", $time['minutes']).':'.sprintf("%02d", $time['seconds']).' / ';
			if($total['hours']!=0)
			{
				$timeFormatted = $timeFormatted.$total['hours'].":";
			}
			$timeFormatted = $timeFormatted.sprintf("%02d", $total['minutes']).":".sprintf("%02d", $total['seconds']);
			echo "$timeFormatted</p>\n";
		}
		if(!empty($results['result']['paused']) && ($results['result']['paused'])) {
			echo "\t\t<p>Paused</p>\n";
		}

		//progress bar
		$results = jsonmethodcall("VideoPlayer.GetPercentage");
		$percentage = $results['result'];
		echo "\t\t<div class='progressbar'><div class='progress' style='width:".$percentage."%'></div></div>";

		if(!empty($_GET['style']) && $_GET['style'] == 'm') {
			widgetNowPlayingControls($baseurl);
		}
	} elseif (($results['result']['audio']) == 1) {
		//get playlist items
		$results = jsonmethodcall("AudioPlaylist.GetItems");
		$items = $results['result']['items'];
		$current = $results['result']['current'];

		$thumb = $items[$current]['thumbnail'];
		$artist = $items[$current]['artist'];
		$title = $items[$current]['title'];
		$album = $items[$current]['album'];
		if(strlen($thumb) > 0) {
			echo "\t<div id=\"thumbblock\" class=\"thumbblockaudio\">\n";
			if(!empty($baseurl)) {
				echo "\t\t<img src=\"".$xbmcimgpath.$thumb."\" alt=\"".htmlentities($artist." - ".$album." - ".$title, ENT_QUOTES)."\" />";
			} else {
				echo "\t\t<a href=\"".$xbmcimgpath.$thumb."\" class=\"highslide\" onclick=\"return hs.expand(this)\">\n";
				echo "\t\t\t<img src=\"".$xbmcimgpath.$thumb."\" title=\"Click to enlarge\" alt=\"".htmlentities($artist." - ".$album." - ".$title, ENT_QUOTES)."\" />";
				echo "\t\t</a>\n";
			}
			echo "\t</div>\n";
		}
		echo "\t<p>".$artist."</p>\n";
		echo "\t<p>".$title."</p>\n";
		echo "\t<p>".$album."</p>\n";

		//progress time
		$results = jsonmethodcall("AudioPlayer.GetTime");
		$time = $results['result']['time'];
		$total = $results['result']['total'];
		echo "\t<p>".formattimes($time, $total)."</p>\n";
		if($results['result']['paused']) {
			echo "\t<p>Paused</p>\n";
		}
		echo "</div>\n";				

		//progress bar
		$results = jsonmethodcall("AudioPlayer.GetPercentage");
		$percentage = $results['result'];
		echo "<div class=\"progressbar\"><div class=\"progress\" style=\"width:".$percentage."%\"></div></div>\n";

		if(!empty($_GET['style']) && $_GET['style'] == 'm') {
			widgetNowPlayingControls($baseurl);
		}
	} else {
		echo "\t<p>Nothing Playing</p>\n";
	} 
	echo "</div>\n";
}
function processCommand($command) {
	global $xbmcimgpath;
	
	switch($command) {
		case "ShowPlaylist":
			$results = jsonmethodcall("Player.GetActivePlayers");
			if (($results['result']['video']) == 1) {
				echo "\t<p>Not Yet Implemented</p>\n";
			} elseif (($results['result']['audio']) == 1) {
				$results = jsonmethodcall("AudioPlaylist.GetItems");

				if (array_key_exists('items', $results['result'])) {
					$items = $results['result']['items'];
					$current = $results['result']['current'];

					$songcount = count($results);
					$i = 0;

					foreach ($items as $queueItem) {
						if ($i > $current) {
							$thumb = $queueItem['thumbnail'];
							$artist = $queueItem['artist'];
							$title = $queueItem['title'];
							$album = $queueItem['album'];
							if(strlen($thumb) > 0) {
								echo "<div id=\"playlist-item-".$i."\" class=\"playlist-item\">\n";
								echo "\t<img src=\"".$xbmcimgpath.$thumb."\" />\n";
							}
							echo "\t<p>".$artist."</p>\n";
							echo "\t<p>".$title."</p>\n";
							echo "\t<p>".$album."</p>\n";
							echo "</div>\n";
							echo "<div class=\"clear-float\"></div>\n";
						}
						$i++;
					}
				}
			}
			break;
		default:
		/*
			XBMC Player Commands
			PlayPause,            Pauses or unpause playback
			Stop,                 Stops playback
			SkipPrevious,         Skips to previous item on the playlist
			SkipNext,             Skips to next item on the playlist
			BigSkipBackward,      
			BigSkipForward,       
			SmallSkipBackward,    
			SmallSkipForward,     
			Rewind,               Rewind current playback
			Forward,              Forward current playback
		*/

		//get active players
		$results = jsonmethodcall("Player.GetActivePlayers");
		//Video Player
		if (($results['result']['video']) == 1) {
			//get playlist items
			$player = "VideoPlayer";
		//Music Player
		} elseif (($results['result']['audio']) == 1) {
			//get playlist items
			$player = "AudioPlayer";
		} else {
			// Nothing Playing
		}
		if(!empty($player) && !empty($command)) {
			$results = jsonmethodcall($player.'.'.$command);
		}
		
		// debugging
		if(!empty($_GET["debug"]) && ($_GET["debug"] == "y")) {
			echo "<br/>Call: <pre>";
			echo print_r($request,1);
			echo "</pre><br/>";
			echo "<br/>Result: <pre>";
			echo print_r($result,1);
			echo "</pre><br/>";
		}
	}
}

if (!empty($_GET['ajax']) && ($_GET['ajax'] == "w")) {
	require_once "../config.php";
	require_once "../functions.php";
	displayNowPlaying();
}
?>
<?php
if (!empty($_GET['ajax']) && ($_GET['ajax'] == "c")) {
	require_once "../config.php";
	require_once "../functions.php";
	if (!empty($_GET['command'])) {
		$command = $_GET["command"];
		processCommand($command);
	} else {
		echo "<br/>\n";
		echo "<p><strong>Invalid Request<strong></p>\n";
		echo "<p>Call: <pre>\n";
		echo print_r($_GET,1);
		echo "\n</pre>\n</p>\n";
	}
}

if (!empty($_GET['style']) && (($_GET['style'] == "w") || ($_GET['style'] == "s"))) {
	if ($_GET['style'] == "w") {
?>
<html>
	<head>
		<title>Media Front Page - Now Playing</title>
		<link rel="stylesheet" type="text/css" href="css/front.css">
	</head>
	<body>
<?php
		displayNowPlaying();
?>
	</body>
</html>
<?php
	} else {
		displayNowPlaying();
	}
}
?>
