<?php
require('../../config.php');
global $transmission_url, $transmission_admin, $transmission_pass;
require_once('transmission_api.php');
//echo $transmission_url;

$rpc = new TransmissionRPC($transmission_url);
$rpc->username = $transmission_admin;
$rpc->password = $transmission_pass;

if($_GET['stop']=='all'){
	$rpc->stop(array());
} 
if($_GET['start']=='all'){
	$rpc->start(array());
}

if(!empty($_GET['dllimit'])){
	$rpc->sessionSet(array(),array('speed-limit-down'=>intval($_GET['dllimit']),'speed-limit-down-enabled'=>true));
	echo "Download speed set to ".$_GET['dllimit'];
}
if(!empty($_GET['ullimit'])){
	$rpc->sessionSet(array(),array('speed-limit-up'=>intval($_GET['ullimit']),'speed-limit-up-enabled'=>true));
	echo "Upload speed set to ".$_GET['ullimit'];
}


$torrents = $rpc->get();

if(!empty($torrents->arguments->torrents)){
	foreach ($torrents->arguments->torrents as $item){
		$id = $item->id;
		
		if($_GET['start']==$id){
			$rpc->start($id);
		}
		if($_GET['stop']==$id){
			$rpc->stop($id);
		}
		if($_GET['remove']==$id){
			$rpc->remove($id,true);
		}
	}
}
?>