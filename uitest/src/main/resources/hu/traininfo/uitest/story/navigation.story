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

Story: testing navigation (buttons and links) with as few and simple steps as possible

Scenario: check that application version is the same as expected to be tested
 
Given that I navigate to vonatinfo.hu/version.php
Then I should see the traininfo version under testing

Scenario: check Menetrend button

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
Then I should see timetable.php page within 2 seconds
Then I should see BUDAPEST* - Esztergom title in the main title field within 3 seconds

Scenario: check navigation from timetable page back to search page

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click on Új keresés link
Then I should see search.php page within 2 seconds
And I should see BUDAPEST* in field labelled Honnan:
And I should see Esztergom in field labelled Hova: