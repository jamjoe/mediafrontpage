<?php 
$wIndex["wSystem"] = array("name" => "System info", "type" => "inline", "function" => "wSystem();");

function wSystem(){
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']); 

        // Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari. 
        if (preg_match('/opera/', $userAgent)) { 
            $name = 'Opera'; 
        } 
        elseif (preg_match('/chrome/', $userAgent)) { 
            $name = 'Chrome'; 
        } 
        elseif (preg_match('/webkit/', $userAgent)) { 
            $name = 'Safari'; 
        } 
        elseif (preg_match('/msie/', $userAgent)) { 
            $name = 'Internet Explorer'; 
        } 
        elseif (preg_match('/mozilla/', $userAgent) && !preg_match('/compatible/', $userAgent)) { 
            $name = 'Firefox'; 
        } 
        else { 
            $name = 'unrecognized'; 
        } 

        // What version? 
        if (preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches)) { 
            $version = $matches[1]; 
        } 
        else { 
            $version = 'unknown'; 
        } 

        // Running on what platform? 
        if (preg_match('/linux/', $userAgent)) { 
            $platform = 'Linux'; 
        } 
        elseif (preg_match('/macintosh|mac os x/', $userAgent)) { 
            $platform = 'Mac OS'; 
        } 
        elseif (preg_match('/windows|win32/', $userAgent)) { 
            $platform = 'Windows'; 
        } 
        else { 
            $platform = 'Unrecognized'; 
        } 

/*
        return array( 
            'name'      => $name, 
            'version'   => $version, 
            'platform'  => $platform, 
            'userAgent' => $userAgent 
        ); 
*/
    	echo "<p>Browser: $name $version</p>";
    	echo "<p>OS: $platform</p>";
    	echo "<p>Server Address: ".$_SERVER['SERVER_ADDR']."</p>";
    	echo "<p>Server name: ".$_SERVER['SERVER_NAME']."</p>";
    	echo "<p>Server software: ".$_SERVER['SERVER_SOFTWARE']."</p>";
    	echo "<p>Server protocol: ".$_SERVER['SERVER_PROTOCOL']."</p>";
    	echo "<p>Request time: ".date('l, F j, Y g:i a', $_SERVER['REQUEST_TIME'])."</p>";
    	echo "<p>HTTP Referrer: ".$_SERVER['HTTP_REFERER']."</p>";
    	echo "<p>Remote address: ".$_SERVER['REMOTE_ADDR']."</p>";
    	echo "<p>Remote host: ".$_SERVER['REMOTE_HOST']."</p>";
    	echo "<p>Remote port: ".$_SERVER['REMOTE_PORT']."</p>";
    	echo "<p>Server port: ".$_SERVER['SERVER_PORT']."</p>";
    	echo "<p>Request URI: ".$_SERVER['REQUEST_URI']."</p>";
    	//print_r($userAgent);
    
}
?> 