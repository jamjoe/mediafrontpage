###MediaFrontPage

MediaFrontPage is a HTPC Web Program Organiser.
Your HTPC utilises a number of different programs to do certain tasks. What MediaFrontPage does is creates a user specific web page that will be your nerve centre for everything you will need.

![preview thumb](http://img715.imageshack.us/img715/1564/screenshot20101118at120.png)

MediaFrontPage can make use of, but is not limited to, the following projects:

* Sick Beard
* CouchPotato
* SabNZBd+
* SubSonic
* Transmission (WebGUI)
* uTorrent (WebGUI)
* XBMC (WebGUI)
* JSON
* JQUERY

## Dependencies

MediaFrontPage requires aa Apache/PHP Webserver to be running on the machine or network and must have PHP Curl configured correctly.


## Bugs

This project is always being updated by like minded individuals and bugs will exist. If you find a bug, please report it at [XBMC Support Thread] (http://forum.xbmc.org/showthread.php?t=8330), and include as much information as possible.

## Install

1. Clone the Git to your required directory, usually htdocs or www.
(sudo) git clone git://github.com/gugahoi/mediafrontpage.git "/var/www"

3. Open default-config.php with gedit or nano and edit appropriately

4. Rename default-config.php to config.php

5. Rename default-layout.php to layout.php

6. Ensure file permissions allow web server to write to layout.php eg use chmod
(sudo) chmod +arw 777 layout.php

7. Install PHP-Curl
(sudo) apt-get install php5-curl


OPTIONAL
--------
8.a) Sickbeard Image Cache to improve loading speeds. Create a folder named sbpcache 
(sudo) mkdir /var/www/sbpcache

8.b) Give MFP write permissions to the Cache folder
(sudo) chmod +arw 777 /var/www/spbcache		

## List of available Widgets 5th May 2011

* XBMC Control
* XBMC Library
* Coming Episodes
* Hard Drive Status
* Now Playing
* RSS Feed
* SabNZBd Status
* NZB Search
* TrakT Last Watched
* Transmission
* uTorrent
* JDownloader (WIP)

There is an Example widget inside the Widget folder that gives an idea on how to create your own.