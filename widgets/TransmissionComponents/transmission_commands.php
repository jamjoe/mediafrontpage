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
			$rpc->remove($id);
		}


	}
}
?>