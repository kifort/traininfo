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
class TripCollection{
	public $tripDate; //Date
	public $userFromStation; //Station
	public $userViaStation; //Station
	public $userToStation; //Station
	public $htmlContent; //String
	public $officialLink; //URL
	public $trips; //Trip[]	
}

class Trip{
	public $tripChapters; //TripChapter[]
	public $tickets; //Tickets
	
	public function getBeginTime(){ //TrainTimes
		//return $this->tripChapters[0]->train->timetables[$this->tripChapters[0]->userFromStation->stationName][TimetableType.OFFICIAL]->departure;
		//return $this->tripChapters[0]->train->timetable[$this->tripChapters[0]->userFromStation->stationName]->official->departure;
		return $this->tripChapters[0]->train->timetable[$this->tripChapters[0]->userFromStation->stationName];
	}

	public function getEndTime(){ //TrainTimes
		//return $this->tripChapters[count($this->tripChapters)-1]->train->timetables[$this->tripChapters[count($this->tripChapters)-1]->userToStation->stationName][TimetableType.OFFICIAL]->arrival;
		return $this->tripChapters[count($this->tripChapters)-1]->train->timetable[$this->tripChapters[count($this->tripChapters)-1]->userToStation->stationName];
	}
	
// 	public function getTickets(){ //Tickets[]
// 		$tickets = array();
// 		foreach($this->tripChapters as $tripChapter){
// 			array_push($tickets, $tripChapter->$tickets);
// 		}
// 		return tickets;
// 	}
	
// 	public function getCumulatedFirstClassTicket(){ //Ticket
// 		$cumulatedTicket = $this->tripChapters[0]->$tickets->firstClass;
// 		$cumulatedTicket->price = 0;
// 		foreach($this->tripChapters as $tripChapter){
// 			$ticket = $this->tripChapters[0]->$tickets->firstClass;
// 			if(!isset($ticket)){
// 				$ticket = $this->tripChapters[0]->$tickets->secondClass;
// 			}
// 			$cumulatedTicket->price += $ticket.price;
// 			if($ticket->discount != $cumulatedTicket->discount || $ticket->priceUnit != $cumulatedTicket->priceUnit){
// 				//TODO handle that cumulated ticket can't be creted
// 				return null; 
// 			}
// 		}
// 		return $cumulatedTicket;
// 	}
	
// 	public function getCumulatedSecondClassTicket(){ //Ticket
// 		$cumulatedTicket = $this->tripChapters[0]->$tickets->secondClass;
// 		$cumulatedTicket->price = 0;
// 		foreach($this->tripChapters as $tripChapter){
// 			$ticket = $this->tripChapters[0]->$tickets->secondClass;
// 			$cumulatedTicket->price += $ticket.price;
// 			if($ticket->discount != $cumulatedTicket->discount || $ticket->priceUnit != $cumulatedTicket->priceUnit){
// 				//TODO handle that cumulated ticket can't be creted
// 				return null;
// 			}
// 		}
// 		return $cumulatedTicket;
// 	}
}

class TripChapter{
	public $train; //Train
	//public $stations; //Station[]
	public $userFromStation; //Station
	public $userToStation; //Station
	//public $tickets; //Tickets
	
	public function isValid(){ //Boolean
		//trains must have the same date as the trip
		//stations must contain stations without duplication
		//stations must contain first, last, userFrom, userTo and current stations
	}
}

class Train{
    public $trainNumber; //Integer unique within one day
    public $trainDate; //Date
    public $trainType; //String - szemely, gyors, zonazo, ...
    public $highestCarriageClass; //CarriageClass
    public $otherInformation; //String - e.g. vaganyzar, felsovezetekszakadas, ...
    public $dayRestriction; //String e.g. munkanapokon, hétvégén, ...
    public $services; //Service[]
    public $officialLink; //URL
    
    public function getId(){
    	return $trainNumber . " " . $trainDate;
    }
    
    public function distanceFromUserStation() {
        //TODO return calculation
    }
    
    public function isValid(){ //Boolean
    	//valid trainNumber, trainDate
    }
}

class TrainDetails extends Train{
	public $stations; //Station[]
	public $timetable; //Map<Station->stationName, TrainTimes>
	public $platforms; //Map<Station->stationName, Integer>
	public $distances; //Map<Station->stationName, Distance> - distance in kilometers and minutes from the previous station
	public $lastStation; //Station
	public $nextStation; //Station
	public $htmlContent; //String
	
	public function distanceBetweenStations($station1, $station2) { //Integer km
		//TODO return calculation
	}
	
	public function distanceFromFirstStation($station) { //Integer km
		//TODO return calculation
	}
	
	public function late($station) { //Time in minutes
		//TODO return calculation
	}
	
	public function firstStation(){
		return $this->stations[0];
	}
	
	public function lastStation(){
		return $this->stations[count($this->stations)-1];
	}
	
	public function isValid(){ //Boolean
		//every station in platforms and distances exist in timetable
		//one OFFICIAL and optionally one ESTIMATED or ACTUAL timetable for each station  
	}
}

class Station{
	public $stationName; //String
	public $otherInformation; //String
	public $services; //Service[]
	public $officialLink; //URL
	
	public function isValid(){ //Boolean
		//valid stationName
	}
}

class StationDetails extends Station{
	public $timetable; //Map<Train, TrainTimes>
	public $platforms; //Map<Train, Integer>
	public $htmlContent; //String
	
	public function late($station) { //Time in minutes
		//TODO return calculation
	}
	
	public function isValid(){ //Boolean
		//every train in platforms exist in timetable
		//one OFFICIAL and optionally one ESTIMATED or ACTUAL timetable for each train
	}
}

class Distance{
	public $distanceInKilometers; //Integer
	public $distanceInMinutes; //Integer TODO add actual and estimated time distances
}

class TrainTimes{
	public $official; //TrainTime
	public $actual; //TrainTime
	public $estimated; //TrainTime
}

class TrainTime{
// 	public $type; //TimetableType
	public $arrival; //Time string in format of hh:mm
	public $departure; //Time string in format of hh:mm
}

class Tickets{
	public $firstClass; //Ticket
	public $secondClass; //Ticket
}

class Ticket{
	//public $carriageClass; //CarriageClass
	public $discount; //String
	public $price; //Double
	public $priceUnit; //String
}

class Service{
	public $name; //ServiceName (enum, not unique by train!)
    public $description; //String - static information
	public $note; //String - dynamic information; e.g. date restriction of the service
}

class CarriageClass
{
    const FIRST_CLASS = 1;
    const SECOND_CLASS = 2;
}

class ServiceName
{
	const WHEELCHAIR = 101;
	const WHEELCHAIR_ELEVATOR = 102;
	const WHEELCHAIR_TOILET = 103;

}

class TrainServiceName extends ServiceName
{
	const CARRIAGE_CLASS1 = 1; //same as CarriageClass.FIRST_CLASS
	const CARRIAGE_CLASS2 = 2; //same as CarriageClass.SECOND_CLASS
	const BIKE = 113;
	const NOBIKE = 114;
	const BUDAPEST_PASS = 115;
	const PRINTABLE_TICKET = 116;
	const WHEELCHAIR = 117;
	const WHEELCHAIR_ELEVATOR = 118;
	const WHEELCHAIR_TOILET = 119;
	const RUNS_ALWAYS = 120;
	const SEAT_RESERVATION = 121;
	const BIKE_PLACE_RESERVATION = 122;
	const WIFI = 123;
	const RESTAURANT = 124;
	
}

class StationServiceName extends ServiceName
{
	const E_TICKET = 131;
	const TICKET_AUTOMAT = 132;

}

// class TimetableType{
// 	const OFFICIAL = 301;
// 	const ACTUAL = 302;
// 	const ESTIMATED = 303;
// }
?>
