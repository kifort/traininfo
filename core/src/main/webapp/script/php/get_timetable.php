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
//TOOD extract price info
require "script/php/post_request.php";

class OfficialEdition{
	//TODO get edition from DB or shared memory
	public static $officialEditionId = "5081BCB6";
}

function getTripCollection($searchParameters){
	require "script/php/init.php";
	libxml_use_internal_errors(true);
	
	//Cache for trains and stations
	$trainCache = array(); //train number -> train details (Integer -> TrainDetails)
	$stationCache = array(); //station name -> station (Integer -> Station)
	
	//Count trip day
	$selectedDay = mktime(0,0,0,date("m"),date("d"),date("Y"));
	if($searchParameters["when"]=="Holnap"){
		$selectedDay = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
	} else if($searchParameters["when"]=="Holnapután"){
		$selectedDay = mktime(0,0,0,date("m"),date("d")+2,date("Y"));
	}
	$selectedDayStr = date("y.m.d", $selectedDay); //12.10.15
	
	//Get ed (edition?) parameter from http://elvira.mav-start.hu/
	$officialSearchHtml = file_get_contents("http://elvira.mav-start.hu/elvira.dll/xslvzs/?language=1");
	
	$officialSearchDom = new DOMDocument;
	$officialSearchDom->loadHTML($officialSearchHtml);
	$officialSearchXPath = new DOMXpath($officialSearchDom);
	
	$officialEditionId = trim($officialSearchXPath->query("//*[@name='ed']/@value")->item(0)->nodeValue);
	//echo "old officialEditionId: " . OfficialEdition::$officialEditionId . "<br/>";
	//echo "new officialEditionId: " . $officialEditionId . "<br/>";
	
	//Update stored edition if needed
	if(OfficialEdition::$officialEditionId != $officialEditionId){
		OfficialEdition::$officialEditionId = $officialEditionId;
		//TODO store edition in DB or shared memory
		//TODO signal edition update via email
	}
	
	//Create POST and GET query for official timetable
	$officialSearchPostData = array(
			"ed" => $official_edition_id,
			"mikor" => "-1",
			"isz" => 0,
			"language" => 1,
			"k" => "",
			"ref" => "",
			"retur" => "",
			"nyit" => "",
			"vparam" => "",
			"i" => mb_convert_encoding($searchParameters["fromStation"], "ISO-8859-2", "UTF-8"),
			"e" => mb_convert_encoding($searchParameters["toStation"], "ISO-8859-2", "UTF-8"),
			"v" => mb_convert_encoding($searchParameters["viaStation"], "ISO-8859-2", "UTF-8"),
			"d" => $selectedDayStr,
			"u" => "27",
			"go" => "Timetable"
	);
	
	$officialSearchGetData = "";
	foreach($officialSearchPostData as $key => $value){
		$officialSearchGetData .= $key . "=" . urlencode($value) . "&";
	}
	$officialSearchGetData = substr($officialSearchGetData, 0, strlen($officialSearchGetData)-1);
	
	//Send query for official timetable
	$officialSearchResult = post_request("http://elvira.mav-start.hu/elvira.dll/xslvzs/uf", $officialSearchPostData);
	
	//Check HTTP response code
	if ($officialSearchResult["status"] == "ok"){
		//Parse official timetable response with DOM 
		$officialSearchResultDom = new DOMDocument;
		$officialSearchResultDom->loadHTML($officialSearchResult["content"]);
		$officialSearchResultXPath = new DOMXpath($officialSearchResultDom);
		
		//Check timetable existence in the official timetable response
		if($officialSearchResultXPath->query("//*[@id='timetable']")->length == 0)
		{
			header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Sikertelen keresés") );
		}
		
		//Initialize trip collection
		$tripCollection = new TripCollection();
		
		//Extract and set trip date from official timetable HTML
		$tripDateStr = $officialSearchResultXPath->query("//div[@class='rrtftop']")->item(0)->nodeValue;
		$tripDateStr = substr($tripDateStr, 0, stripos($tripDateStr, ",")) . ".";
		$tripDateYearsMonthsAndDays = explode(".", $tripDateStr);
		$tripCollection->tripDate = new DateTime();
		$tripCollection->tripDate->setDate($tripDateYearsMonthsAndDays[0], $tripDateYearsMonthsAndDays[1], $tripDateYearsMonthsAndDays[2]);
		
		//Set link to the offical webpage showing the searched timetable
		$tripCollection->officialLink = "http://elvira.mav-start.hu/elvira.dll/xslvzs/uf? ". $officialSearchGetData;
		
		//Set HTML of the offical webpage showing the searched timetable
		$tripCollection->htmlContent = $officialSearchResult["content"];
		
		//Set first station of trip
		$tripCollection->userFromStation = getCachedStation($searchParameters["fromStation"], $stationCache);
		
		//Set first station of trip
		//$firephp->log($searchParameters["viaStation"], "viaStation");
		if(isset($searchParameters["viaStation"]) && strlen($searchParameters["viaStation"]) > 0){
			$tripCollection->userViaStation = getCachedStation($searchParameters["viaStation"], $stationCache);
		}
		
		//Set Last station of trip
		$tripCollection->userToStation = getCachedStation($searchParameters["toStation"], $stationCache);
		
		//Extract discount
		$discountNode = $officialSearchResultXPath->query("//*[@id='timetable']/table/thead/tr[1]/th[7]/a")->item(0);
		if(isset($discountNode)){
			$discount = trim($discountNode->nodeValue);
		}
		
		//Initialize array containing the trips
		$tripCollection->trips = array();
		
		//Iterate over trips from official timetable HTML
		$tripNodes = $officialSearchResultXPath->query("//*[@id='timetable']/table/tbody/tr[@style]");
		$tripIndex = 1;
		foreach($tripNodes as $tripNode) {
			//Initialize trip
			$trip = new Trip();
			
			//Extract trip trip prices from HTML
			$trip->tickets = new Tickets();
			$firstClassPriceNode = $officialSearchResultXPath->query("td[7]/text()", $tripNode)->item(0);
			//If nodeValue is two charavter long, it means some special characters in the official HTML that looks like space, but it isn't
			if(isset($firstClassPriceNode) && isset($firstClassPriceNode->nodeValue) && strlen($firstClassPriceNode->nodeValue>2)){
			    $trip->tickets->firstClass = new Ticket();
				$trip->tickets->firstClass->discount = $discount;
				$trip->tickets->firstClass->price = trim($firstClassPriceNode->nodeValue);
   				$priceAndUnit = explode(" ", $trip->tickets->firstClass->price);
   		        if(count($priceAndUnit) == 2 ){
   		          $trip->tickets->firstClass->price = $priceAndUnit[0];
  				  $trip->tickets->firstClass->priceUnit = $priceAndUnit[1];
				}//if price and price unit
			}//if firstClassPriceNode
			
			$secondClassPriceNode = $officialSearchResultXPath->query("td[8]/text()", $tripNode)->item(0);
			if(isset($secondClassPriceNode) && isset($secondClassPriceNode->nodeValue) && strlen($secondClassPriceNode->nodeValue>2)){
			    $trip->tickets->secondClass = new Ticket();
				$trip->tickets->secondClass->discount = $discount;
				$trip->tickets->secondClass->price = trim($secondClassPriceNode->nodeValue);
				$priceAndUnit = explode(" ", $trip->tickets->secondClass->price);
				if(count($priceAndUnit) == 2 ){
				  $trip->tickets->secondClass->price = $priceAndUnit[0];
				  $trip->tickets->secondClass->priceUnit = $priceAndUnit[1];
				}//if price and price unit
			}//if $secondClassPriceNode
				
			//Initialize array containing the trip chapters
			$trip->tripChapters = array();
			
			//Iterate over trip chapters from official timetable HTML
			$tripChapterNodes = $officialSearchResultXPath->query("//*[@id='info".$tripIndex++."']/table/tbody/tr");
			foreach($tripChapterNodes as $tripChapterNode) {
				//echo "tripChapterNode: " . $officialSearchResultDom->saveXML($tripChapterNode) . "<br/>";
				
				//Extract the name of the station from trip chapter details HTML
				$stationName = trim($officialSearchResultXPath->query("td[1]/a", $tripChapterNode)->item(0)->nodeValue);
				
				//Initialize station
				$station = getCachedStation($stationName, $stationCache);
				
				//Set link to the offical webpage showing the details of a train
				if(!isset($station->officialLink)){
					$station->officialLink = "http://elvira.mav-start.hu/elvira.dll/xslvzs/". $officialSearchResultXPath->query("td[1]/a/@href", $tripChapterNode)->item(0)->nodeValue;
				}
				//TODO get station details if needed
				
				//Extract the link to the train of trip chapter from official timetable HTML
				$tripChapterTrainNodeList = $officialSearchResultXPath->query("td[6]/a", $tripChapterNode);
				$tripChapterTrainNode = $tripChapterTrainNodeList->item(0);
				
				//Get trip chapter type (train or local transportation) from the link to the train
				if($tripChapterTrainNode){
					$tripChapterType = getTripChapterType($tripChapterTrainNode, $officialSearchResultXPath);
				}
				
				//echo "tripChapterType2: " . $tripChapterType . "<br>";
				if($tripChapterType == "FROM_STATION_WITH_TRAIN"){
					//Set type of the next row in trip details
					$tripChapterType = "TO_STATION_WITH_TRAIN";
					
					//Create new trip chapter
					$tripChapter = new TripChapter();
					
					//Set first station of trip trip chapter
					$tripChapter->userFromStation = $station;
					
					//Get train number from the link to the train
					$trainNumber = trim($tripChapterTrainNodeList->item(0)->nodeValue);
					
					//Initialize train
					$tripChapter->train = getCachedTrain($trainNumber, $trainCache);
				}//if FROM_STATION_WITH_TRAIN
				else if($tripChapterType == "TO_STATION_WITH_TRAIN"){
					//Set type of the next row in trip details
					$tripChapterType = "UNKNOWN";
					
					//Set last station of trip chapter
					$tripChapter->userToStation = $station;
					
					//Set train details from trip chapter details HTML
					$previousTripChapterNode = $officialSearchResultXPath->query("preceding-sibling::*[1]", $tripChapterNode)->item(0);
					setTrainDetails($tripCollection, $tripChapter, $previousTripChapterNode, $officialSearchResultXPath, $stationCache);
					
					array_push($trip->tripChapters, $tripChapter);
					//echo "tripChapters: " . count($trip->tripChapters) . "<br/>";					
				} else if($tripChapterType == "FROM_STATION_WITH_LOCAL_TRANSPORTATION"){ //local transportation from station sh10
					//Set type of the next row in trip details
					$tripChapterType = "TO_STATION_WITH_LOCAL_TRANSPORTATION";
					//TODO TBD
				} else if($tripChapterType == "TO_STATION_WITH_LOCAL_TRANSPORTATION"){ //local transportation to station sh11
					//Set type of the next row in trip details
					$tripChapterType = "UNKNOWN";
					//TODO TBD
				} else {
					//TODO handle unexpected row type; signal unexpected row type via email
				}
			}//foreach tripChapterNode
			
			array_push($tripCollection->trips, $trip);
		}//foreach tripNode
	}//if search ok
	else {
		header( "Location: " . $htmlBaseHref . "/search.php?errorMessage=" . urlencode("Sikertelen keresés") );
	}
	
	return $tripCollection;
}//getTripCollection

function getCachedStation($stationName, $stationCache){
	if(array_key_exists($stationName, $stationCache)){
		//Get from station cache
		$station = $stationCache[$stationName];
	} else{
		//Create new station with name from search parameter and store in station cache
		$station = new Station();
		$station->stationName = $stationName;
		$stationCache[$station->stationName] = $station;
	}
	return $station;
}//getCachedStation

function getCachedTrain($trainNumber, $trainCache){
	if(array_key_exists($trainNumber, $trainCache)){
		$train = $trainCache[$trainNumber];
	} else{
		$train = new TrainDetails();
		$train->trainNumber = $trainNumber;
		$stationCache[$train->trainNumber] = $train;
	}
	return $train;
}//getCachedTrain

function getTripChapterType($tripChapterTrainNode, $officialSearchResultXPath){
	$tripChapterType = "UNKNOWN";
	$tripChapterTypeNode = $officialSearchResultXPath->query("@href", $tripChapterTrainNode)->item(0);
	if($tripChapterTypeNode){
		$tripChapterType = $tripChapterTypeNode->nodeValue;
		$tripChapterType = substr($tripChapterType, 0, 2);
		//echo "$tripChapterType1: " . $tripChapterType . "<br/>";
		if($tripChapterType == "vt"){
			$tripChapterType = "FROM_STATION_WITH_TRAIN";
		} else if($tripChapterType == "hk"){
			$tripChapterType = "FROM_STATION_WITH_LOCAL_TRANSPORTATION";
		} else{
			//TODO handle unexpected trip chapter type; signal unexpected row type via email
		}
	}//if $tripChapterTypeNode
	return $tripChapterType;
}//getTripChapterType

function setTrainDetails($tripCollection, $tripChapter, $tripChapterNode, $officialSearchResultXPath, $stationCache){
	$train = $tripChapter->train;
	
	//Set train date to trip date
	$train->trainDate = $tripCollection->tripDate;
	
	//Set highest carriage class from trip chapter HTML
	$train->highestCarriageClass = 1;
	$secondClassImage = $officialSearchResultXPath->evaluate("count(td[7]/img[@src='http://elvira.mav-start.hu/fontgif/36.gif'])", $tripChapterNode);
	if($secondClassImage == "1"){
		$train->highestCarriageClass = 2;
	}
	//echo "train->highestCarriageClass: _" . $train->highestCarriageClass . "_<br/>";
	
	//Set link to the offical webpage showing the train details from trip chapter HTML
	//echo $tripChapterNode.C14N() . "<br/>";
	$train->officialLink = "http://elvira.mav-start.hu/elvira.dll/xslvzs/". $officialSearchResultXPath->query("td[6]/a/@href", $tripChapterNode)->item(0)->nodeValue;
	//echo "train->officialLink: _" . $train->officialLink . "_<br/>";
	
	//TODO set $train->otherInformation
	
	//Set train services from trip chapter HTML
	$train->services = getTrainServices($officialSearchResultXPath, $tripChapterNode);
	
	//Get official train information HTML
	//TODO test HttpRequest instead of file_get_contents
// 	$trainDetailsHttpRequest = new HttpRequest($train->officialLink, HttpRequest::METH_GET);
// 	try {
// 		$trainDetailsHttpRequest->send();
// 		$trainDetailsHttpResponse = http_get($train->officialLink);
		
// 		if ($trainDetailsHttpRequest->getResponseCode() == 200) {
// 			if($trainDetailsHttpResponse){
// 				$trainDetailsHtml = $trainDetailsHttpRequest->getResponseBody();
// 				$trainDetailsHtml = http_parse_message($trainDetailsHttpResponse)->body;
		
		$trainDetailsHtml = file_get_contents($train->officialLink);
		//echo $trainDetailsHtml . "<br/>";
		
		//Parse official train information response with DOM
		$trainDetailsDom = new DOMDocument;
		$trainDetailsDom->loadHTML($trainDetailsHtml);
		$trainDetailsXPath = new DOMXpath($trainDetailsDom);
	
		//Set HTML of the offical webpage showing the train information
		$train->htmlContent = $trainDetailsHtml;
	
		//Set train type from official train information HTML
		$train->trainType = trim($trainDetailsXPath->query("//*[@id='tul']/h2/span")->item(0)->nodeValue);
		//echo "train->trainType: _" . $train->trainType . "_<br/>";
	
		//Set day restriction if any from official train information HTML
		$dayRestrictionNode = $trainDetailsXPath->query("//*[@id='kozlekedik']/ul/li[2]");
		if($dayRestrictionNode){
			$train->dayRestriction = trim($dayRestrictionNode->item(0)->nodeValue);
			//echo "train->dayRestriction: _" . $train->dayRestriction . "_";
		}
	
		//Initialize train stations, timetable, platforms and distances
		$train->stations = array();
		$train->timetable = array();
		$train->platforms = array();
		$train->distances = array();
		
		//Iterate over stations of the train
		foreach( $trainDetailsXPath->query("//*[@id='menetrend']/table/tbody/tr[position()>2]") as $stationNode){
			//TODO? reduce station details between $tripChapter->fromStation and $tripChapter->toStation, but be careful with station cache
			
			//Initialize station
			$stationName = $trainDetailsXPath->query("td[2]/a", $stationNode)->item(0)->nodeValue;
			$station = getCachedStation($stationName, $stationCache);
			//echo "station->stationName: " . $station->stationName . "<br/>";
			
			//Set link to the offical webpage showing the details of a train
			if(!isset($station->officialLink)){
				$station->officialLink = "http://elvira.mav-start.hu/elvira.dll/xslvzs/". $trainDetailsXPath->query("td[2]/a/@href", $stationNode)->item(0)->nodeValue;
			}
			
			//Create official time
			$officialTime = new TrainTime();
			$officialTime->arrival = trim($trainDetailsXPath->query("td[3]", $stationNode)->item(0)->nodeValue);
			$officialTime->departure = trim($trainDetailsXPath->query("td[4]", $stationNode)->item(0)->nodeValue);
			//echo "officialTime->arrival: " . $officialTime->arrival . "<br/>";
			//echo "officialTime->departure: " . $officialTime->departure . "<br/>";
				
			//Create actual time
			$actualTime = new TrainTime();
			$actualArrivalNode = $trainDetailsXPath->query("td[5]/span", $stationNode)->item(0);
			if($actualArrivalNode){
				$actualTime->arrival = trim($actualArrivalNode->nodeValue);
				//Set the last station of the train that the train already accessed
				$train->lastStation = $station;
			}
			$actualDepartureNode = $trainDetailsXPath->query("td[6]/span", $stationNode)->item(0);
			if($actualDepartureNode){
				$actualTime->departure = trim($actualDepartureNode->nodeValue);
			}
			
			//Create estimated time
			$estimatedTime = new TrainTime();
			$estimatedArrivalNode = $trainDetailsXPath->query("td[7]/span", $stationNode)->item(0);
			if($estimatedArrivalNode){
				$estimatedTime->arrival = trim($estimatedArrivalNode->nodeValue);
				//Set the next station of the train
				if($train->nextStation == null){
					$train->nextStation = $station;
				}
			}
			$estimatedDepartureNode = $trainDetailsXPath->query("td[8]/span", $stationNode)->item(0);
			if($estimatedDepartureNode){
				$estimatedTime->departure = trim($estimatedDepartureNode->nodeValue);
			}
			
			//Add station to train
			array_push($train->stations, $station);
			
			//Set train times at the current station 
			$trainTimes = new TrainTime();
			$trainTimes->official = $officialTime;
			$trainTimes->actual = $actualTime;
			$trainTimes->estimated = $estimatedTime;
			//array(TimetableType.OFFICIAL=>$officialTime, TimetableType.ACTUAL=>$actualTime, TimetableType.ESTIMATED=>$estimatedTime);
			$train->timetable[$station->stationName] = $trainTimes;
			//echo "tripChapter->train->timetables_: " . count($tripChapter->train->timetables) . "<br/>";
			
			//Set train platform at the current station
			$platformNode = $trainDetailsXPath->query("td[9]", $stationNode)->item(0);
			if($platformNode){
				$train->platforms[$station->stationName] = trim($platformNode->nodeValue);
			}
			
			//Initialize a distance between two stations of the train
			$distance = new Distance();
			
			//Set distance the previous station of the train in kilometers
			$distance->distanceInKilometers = $trainDetailsXPath->query("td[1]", $stationNode)->item(0)->nodeValue;
			
			//Set official distance the previous station of the train in minutes
			//TODO set actual and estimated time distances 
			$previousDepartureTimeNode = $trainDetailsXPath->query("../preceding-sibling::*[1]/td[4]", $stationNode);
			$arrivalTimeNode = $trainDetailsXPath->query("td[3]", $stationNode);
			if($previousDepartureTimeNode && $arrivalTimeNode){
				//TODO use date_create and  date_diff/date_sub instead of strtotime and date functions
				//$previousDepartureTime = date_create(trim($previousDepartureTimeNode->item(0)->nodeValue));
				//$arrivalTime = date_create(trim($arrivalTimeNode->item(0)->nodeValue));
				//$distance->$distanceInMinutes = date("HH:ii", date_diff($arrivalTime, $previousDepartureTime));
				//$distance->$distanceInMinutes = date("HH:ii", date_sub($arrivalTime, $previousDepartureTime));
				//$distance->$distanceInMinutes = $arrivalTime->diff($previousDepartureTime)->format("HH:ii");
				
				$previousDepartureTime = strtotime(trim($previousDepartureTimeNode->item(0)->nodeValue));
				$arrivalTime = strtotime(trim($arrivalTimeNode->item(0)->nodeValue));
				$distance->distanceInMinutes = date("HH:ii", ($arrivalTime - $previousDepartureTime));
			}
			
			//Add distance to all distances of the train
			$train->distances[$station->stationName] = $distance;
		}//foreach stationNode
	//}
	//} catch (HttpException $httpException) { //echo $httpException; }	
}//setTrainDetails

function getTrainServices($officialSearchResultXPath, $tripChapterNode){
	$services = array();
	foreach( $officialSearchResultXPath->query("td[7]/img", $tripChapterNode) as $trainServiceNode){
		$trainService = new Service();
		$trainServiceImgUrl = $officialSearchResultXPath->query("@src", $trainServiceNode)->item(0)->nodeValue;
		$trainService->description = $officialSearchResultXPath->query("@title", $trainServiceNode)->item(0)->nodeValue;
		$trainService->note = $officialSearchResultXPath->query("following-sibling::text()[1]", $trainServiceNode)->item(0)->nodeValue;
		if($trainServiceImgUrl == "http://elvira.mav-start.hu/xslvzs/res/hpt.gif"){
			$trainService->name = TrainServiceName.PRINTABLE_TICKET;
		} else if($trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/36.gif"){
			$trainService->name = TrainServiceName.CARRIAGE_CLASS2;
		} else if($trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/631.gif"){
			$trainService->name = TrainServiceName.BUDAPEST_PASS;
		} else if($trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/609.gif"){
			$trainService->name = TrainServiceName.RUNS_ALWAYS;
		} else if($trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/61.gif" || $trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/60.gif" ){
			$trainService->name = TrainServiceName.BIKE;
		} else if($trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/319.gif"){
			$trainService->name = TrainServiceName.WHEELCHAIR_ELEVATOR;
		} else if($trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/43.gif"){
			$trainService->name = TrainServiceName.SEAT_RESERVATION;
		} else if($trainServiceImgUrl == "http://elvira.mav-start.hu/fontgif/689.gif"){
			$trainService->name = TrainServiceName.BIKE_PLACE_RESERVATION;
		} else{
			//TODO TBC and signal missing train service via email
		}
	}//foreach trainServiceNode
	return $services;
}//getTrainServices
?>