/**
 * Traininfo - Hungarian train timetable for Amazon Kindle eBook Copyright (C)
 * 2012-2022 Tamás Kifor
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
Given that I navigate to vonatinfo.hu/version.php
Then I should see the traininfo version under testing

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field named fromStation
And I enter Esztergom to field named toStation
And I click on searchBtn button
Then I should see timetable.php page within 2 seconds
And I should see BUDAPEST* - Esztergom title in mainTitle field within 3 seconds