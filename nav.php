<?php
 
  include "config.php";
  echo"<html>";
  echo"  <head>";
  echo"    <title>Navigation</title>";
  echo"    <link rel='stylesheet' type='text/css' href='css/nav.css'>";
  echo"  </head>";
  echo"  <body>";
  echo"    <div id=header>";
  echo"      <div id=home>";
  echo"        <a href='./mediafrontpage.php' target='main'></a>";
  echo"      </div>";
  echo"      <div id=nav-menu>";
  echo"        <ul>";
 foreach( $navlink as $navlinklabel => $navlinkpath) {
    echo"          <li><a href='".$navlinkpath."' target=main>".$navlinklabel."</a></li>";
  }
 foreach( $navlink1 as $navlinklabel => $navlinkpath) {
    echo"          <li><a href='".$navlinkpath."' target=new>".$navlinklabel."</a></li>";
  }
  foreach( $navlink2 as $navlinklabel => $navlinkpath) {
    echo"          <li><a href='".$navlinkpath."' target=main>".$navlinklabel."</a></li>";
  }
echo"        </ul>";
  echo"      </div>";
  echo"    </div>";
 
echo"    <div id=header2>";



echo"      <div id=nav-menu2>";
echo"	       <br> ";
echo"        <ul>";

  foreach( $navlink3 as $navlinklabel => $navlinkpath) {
    echo"         <li><a href='".$navlinkpath."' target=main>".$navlinklabel."</a></li>";
  }
echo"        </ul>";
echo"       </div>";
echo"      </div>";
// echo" <div style='position:absolute; top:10; left:15;'><A HREF='javascript:parent.main.history.back()'><IMG SRC='media/arrowleft.png' HEIGHT='15' WIDTH='10' BORDER='0'></A>&nbsp;&nbsp;&nbsp;<A HREF='javascript:parent.main.history.forward()'><IMG SRC='media/arrowright.png' HEIGHT='15' WIDTH='10' BORDER='0'></div>";
echo"  </body>";
echo"</html>";
