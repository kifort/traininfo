<?php
/**
 * Traininfo - Hungarian train timetable for Amazon Kindle eBook
 * @copyright Copyright (C) 2012-2022 Tamás Kifor
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/.
 *
 * If you have any question contact to Tamás Kifor via email: tamas@kifor.hu
 *
 * @author Tamás Kifor
 */
require "script/php/init.php";
$firstVisiblePage = $htmlBaseHref . '/search.php';
if(isset($_SESSION['screenWidth'])) {
	header( 'Location: ' . $firstVisiblePage );
}
if(isset($_GET['screenWidth'])) {
	$_SESSION['screenWidth']=$_GET['screenWidth'];
	$_SESSION['screenHeight']=$_GET['screenHeight'];
	header( 'Location: ' . $firstVisiblePage );
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="hu">
  <head>
    <title>Vonat Információ</title>
    <base target="_self" href="<?php echo $htmlBaseHref;?>/" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <script language="JavaScript">     
	  document.location="index.php?screenWidth="+screen.width+"&screenHeight="+screen.height;     
    </script>
  </head>
  <body></body>
</html>