<?php
// Only set the $mfpsecured variable to true if you have secured
// MediaFrontPage with a password via .htaccess or some other method
// use at your own risk as this can create a security vulnerability in
// the wControl widget.
$mfpsecured = false;

// Alternativly you can set a unique key here.
$mfpapikey = '';  //

// enter hostname and port of the xbmc json service here. By default 8080
$xbmcjsonservice = "http://USER:PASSWORD@localhost:8080/jsonrpc"; //remove 'USER:PASSWORD@' if your xbmc install does not require a password.
$xbmcimgpath = 'http://localhost:8080/vfs/'; //leave as default if unsure

$xbmcdbconn = array(
		'video' => array('dns' => 'sqlite:/home/xbmc/.xbmc/userdata/Database/MyVideos34.db', 'username' => '', 'password' => '', 'options' => array()),
		'music' => array('dns' => 'sqlite:/home/xbmc/.xbmc/userdata/Database/MyMusic7.db', 'username' => '', 'password' => '', 'options' => array()),
	);
//Example of mysql connections
/*
$xbmcdbconn = array(
		'video' => array(
			'dns' => 'mysql:host=hostname;dbname=videos',
			'username' => '',
			'password' => '',
			'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		),
		'music' => array(
			'dns' => 'mysql:host=hostname;dbname=music',
			'username' => 'username',
			'password' => 'password',
			'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		),
		
	);
*/

// enter path to sickbeards's coming episodes page
$sickbeardcomingepisodes = 'http://user:password@COMPUTER:PORT/sickbeard/comingEpisodes/';
$sickbeardurl = "http://user:password@COMPUTER:PORT/sickbeard/";

// enter SABnzbd+ URL and API key
$saburl = 'http://localhost:8080/sabnzbd/';  // The full URL you use to access SABnzbd.
$sabapikey = '';                             // SABnzbd's API Key found in Config>General.

//CouchPotato home page url
$cp_url =  "http://user:password@COMPUTER:PORT/";		//eg.: http://admin:password@192.168.0.5:5000/

//enter NZBmatrix login
$nzbusername = 'your nzb matrix username';		//username
$nzbapi = 'your nzb matrix api';				//api

//enter nzb.su login
$nzbsuapi = 'your nzb.su api';                // find this in http://nzb.su/profile 
$nzbsudl = '&i=XXXX&r=XXXXXXXXXXXXXXXX';      // find this in http://nzb.su/rss where it says "Add this string to your feed URL to allow NZB downloads without logging in:"

//Choose your default category and website to serch
$preferredSearch = '2';			// Set to 1 for NZBMatrix and 2 for nzb.su
$preferredCategories = '0'; 	// Check README for a list of options. Make sure the option is for the appropriate site.

//uTorrent info
$utorrent_url = "http://localhost:8081/"; //url including port eg:http://localhost:8081/

//Transmission info
$transmission_url 	= "http://localhost:9091/transmission/rpc";	// The url for transmission webserver
$transmission_admin	= "";	// The username for trasmission webui
$transmission_pass 	= "";	// The password for the webui

// enter navigation bar links
$navlink;
$navlink["XBMC"] = "http://localhost:8080";
$navlink["Sickbeard"] = "/sickbeard";
$navlink["Couch Potato"] = "/couchpotato";
$navlink["TV Headend"] = "/tvheadend";
$navlink["Sabnzbd"] = "/sabnzbd";
$navlink["Transmission"] = "http://localhost:9091/transmission/web/";
$navlink["uTorrent"] = "http://localhost:8081/gui/";

// enter shortcut links for control section
$shortcut;
$shortcut["Shutdown XBMC"] = array("cmd" => 'shutdown');
$shortcut["Update XBMC Video Library"] = array("cmd" => 'vidscan');
$shortcut["Clean XBMC Video Library"] = array("xbmcsend" => 'CleanLibrary(video)'); // Optionally add 'host' => 'localhost', 'port' => 9777 to connect to a different machine.
$shortcut["Update XBMC Audio Library"] = array("json" => '{"jsonrpc": "2.0", "method": "AudioLibrary.ScanForContent", "id" : 1 }');
$shortcut["Google"] = "http://www.google.com/";
/*
$shortcut["Input - XBMC"] = "/input/xbmc";
$shortcut["Input - Pay TV"] = "/input/cable";
$shortcut["Input - Games"] = "/input/games";
$shortcut["Now Playing"] = "/nowplaying";
$shortcut["Turn TV On"] = "/tv/on";
$shortcut["Turn TV Off"] = "/tv/off";
$shortcut["Turn Xbox On"] = "/xbox/on";
$shortcut["Turn Xbox Off"] = "/xbox/off";
*/

// enter directories for hard drive section
$drive;
$drive["/"] = "/";
$drive["Sata 1"] = "/media/sata1/";
$drive["Sata 2"] = "/media/sata2/";
$drive["Sata 3"] = "/media/sata3/";
$drive["Sata 4"] = "/media/sata4/";

// enter rss feeds. Ensure sabnzbd > config > index sites is set. Supports cat, pp, script, priority as per the sabnzbd api.
$rssfeeds["NZBMatrix - TV Shows (DivX)"]    = array("url" => "http://rss.nzbmatrix.com/rss.php?subcat=6", "cat" => "tv");
$rssfeeds["NZBMatrix - TV Shows (HD x264)"] = array("url" => "http://rss.nzbmatrix.com/rss.php?subcat=41", "cat" => "tv");
$rssfeeds["NZBMatrix - Movies (DivX)"]      = array("url" => "http://rss.nzbmatrix.com/rss.php?subcat=2", "cat" => "movies");
$rssfeeds["NZBMatrix - Movies (HD x264)"]   = array("url" => "http://rss.nzbmatrix.com/rss.php?subcat=42", "cat" => "movies");
$rssfeeds["NZBMatrix - Music (MP3)"]        = array("url" => "http://rss.nzbmatrix.com/rss.php?subcat=22", "cat" => "music");
$rssfeeds["NZBMatrix - Music (Lossless)"]   = array("url" => "http://rss.nzbmatrix.com/rss.php?subcat=23", "cat" => "music");
$rssfeeds["NZBMatrix - Sports"]             = array("url" => "http://rss.nzbmatrix.com/rss.php?subcat=7", "cat" => "sports");
$rssfeeds["MediaFrontPage on Github"]       = array("url" => "https://github.com/nick8888/mediafrontpage/commits/master.atom", "type" => "atom");

$customStyleSheet = "";
//Example of how to use this
//$customStyleSheet = "css/lighttheme.css";

//Show only posters for coming episodes
//$customStyleSheet = "css/comingepisodes-minimal-poster.css";

//Show only banners for coming episodes
//$customStyleSheet = "css/comingepisodes-minimal-banner.css";

?>
