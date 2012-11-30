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
require_once('TripCollectionClass.php');
require_once('script/php/version.php');
setlocale(LC_ALL, 'hu_HU');
session_start();
$htmlBaseHref = "http://" . $_SERVER["SERVER_NAME"];
$favouriteCookieId = "TRAININFO_FAVOURITES";
require_once('FirePHPCore/FirePHP.class.php');
ob_start();
$firephp = FirePHP::getInstance(true);
$firephp->log($htmlBaseHref, "htmlBaseHref");
?>