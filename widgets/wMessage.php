<?php
$wdgtMessage = array("name" => "Message XBMC", "type" => "inline", "function" => "widgetMessage();");
$wIndex["wMessage"] = $wdgtMessage;

		if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(!empty($_POST['keyword'])){
			$wMessage = 'http://xbmclive:8082/xbmcCmds/xbmcHttp?command=ExecBuiltIn(Notification(NOTICE!,' . $_POST['keyword'] . ',20000))';
			header("Location: " . $wMessage);
		exit;
	}
}

function widgetMessage() {
		$self_post = $_SERVER['PHP_SELF'];
	echo "<form method='post' action='".$self_post."'>Send <b>Notice!</b> <input type='text' style='border:0px; background : #3D3D3D; color:white' name='keyword' id='keyword' />to XBMC.<input type='submit' value='Send'></form>";
}
?>