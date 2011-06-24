<?php
require_once "config.php";
require_once "xbmcjsonlib.php";

function to_readable_size($size) {
	switch (true) {
		case ($size > 1000000000000):
			$size /= 1000000000000;
			$suffix = 'Tb';
		break;
		case ($size > 1000000000):  
			$size /= 1000000000;
			$suffix = 'Gb';
		break;
		case ($size > 1000000):
			$size /= 1000000;
			$suffix = 'Mb';   
		break;
		case ($size > 1000):
			$size /= 1000;
			$suffix = 'Kb';
		break;
		default:
		$suffix = 'b';
	}
	return round($size, 0)." ".$suffix;
}
  
function disk_used_space($value) {
	return disk_total_space("$value") - disk_free_space("$value");
}

function disk_used_percentage($value) {
	return round(disk_used_space("$value") / disk_total_space("$value") * 100, 2);
}

function formattimes($input1, $input2) {
	$seconds1 = $input1 % 60;
	$input1 = floor($input1 / 60);
	$minutes1 = $input1 % 60;
	$hours1 = floor($input1 / 60); 

	$seconds2 = $input2 % 60;
	$input2 = floor($input2 / 60);

	$minutes2 = $input2 % 60;
	$hours2 = floor($input2 / 60); 
	
	if($hours1 > 0 || $hours2 > 0) {
		$output1 = str_pad($hours1,2,'0',STR_PAD_LEFT).":";
		$output2 = str_pad($hours2,2,'0',STR_PAD_LEFT).":";
	} else {
		$output1 = "";
		$output2 = "";
	}
	$output1 = $output1.str_pad($minutes1, 2, '0', STR_PAD_LEFT).":".str_pad($seconds1, 2, '0', STR_PAD_LEFT);
	$output2 = $output2.str_pad($minutes2, 2, '0', STR_PAD_LEFT).":".str_pad($seconds2, 2, '0', STR_PAD_LEFT);

	return $output1.' / '.$output2;
}

function return_array_code($array, $indent = 1, $quote = "\"") {
	//Example call:
	//$layout_code_string = '$arrLayout = '.return_array_code($arrLayout).";\n";

	$first = true;
	$indentstr = str_repeat("\t", $indent);

	$output = "array(\n\t".$indentstr;

	foreach($array as $key => $value) {
		if($first) {
			$first = false;
		} else {
			$output .= ",\n\t".$indentstr;
		}
		
		if(is_array($value)) {
			$value = return_array_code($value, $indent + 1, $quote);
			$output .= $quote.$key.$quote.' => '.$value;
		} else {
			$output .= $quote.$key.$quote.' => '.$quote.$value.$quote;
		}
	}
	$output .= "\n".$indentstr.")";

	return $output;
}
function xbmc_offline() //This function will output some useful stuff if XBMC doesn't appear to be accessible
{
	global $xbmcjsonservice; //We need to get the address for the XBMC host we are trying to talk to first
	$target =  $xbmcjsonservice; //Put the address into a local variable so we can safely do stuff to it
	if (strpos($target, 'http://')!==FALSE) //this will return false if 'http://' isn't found
	{
		$target = substr($target, strpos ($target, 'http://')+7); //if it IS found, this trims it off
	}
	if (strrpos ($target, '/')!==FALSE) //need to remove any subdirectory in the string, like /jsonrpc or /xbmc
	{
		$target = substr($target, 0, strrpos($target, '/'));
	}
	$target = "192.168.1.2:8080";
	if (strrpos ($target, '@')!==FALSE) //next we need to remove any username, if one is present, so we keep everything after the @ symbol
	{
		$target = substr($target, strrpos($target, '@')+1);
	}
	if (strrpos ($target, ':')!==FALSE) //finally, we remove the port # if one is present
	{
		$target = substr($target, 0, strrpos($target, ':'));
	}
	//Now $target should be a string containing just the IP address or hostname for the instance of XBMC that's offline. This allows us to ping that address
	echo "<p>XBMC isn't being reached. ";
	$target = '192.168.1.98';
	exec("sudo ping -c 1 $target 2>&1", $result); //This pings the target once and returns the results in an array named $result
	$result = $result[count($result)-2]; // Now we pull the second to last line out of the output, with the % status
	$result = substr($result, strpos($result, '%')-3, 4); //This processes that line to either '100%' or ', 0%'
	if($result ==', 0%') { echo "$target responded to a ping, but maybe XBMC isn't running?</p>"; }
	if($result =='100%') { echo "$target did not respond to a ping, it may not be up and running.</p>"; }
}
?>