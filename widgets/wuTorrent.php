<?php
$wdgtuTorrent = array("name" => "uTorrent", "type" => "inline", "function" => "widgetuTorrent();");
$wIndex["wuTorrent"] = $wdgtuTorrent;

function widgetuTorrent() {
	echo "<iframe src='http://localhost:8181/gui/' frameborder='0' scrolling='auto' width='100%' height='300'>";
}

?>
