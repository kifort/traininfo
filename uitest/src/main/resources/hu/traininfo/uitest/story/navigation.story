Story: testing navigation (buttons and links) with as few and simple steps as possible
License: http://www.vonatinfo.hu/license/license.txt

Scenario: check that application version is the same as expected to be tested
 
Given that I navigate to vonatinfo.hu/version.php
Then I should see the traininfo version under testing

!-- Scenarios with via station and transfer

Scenario: check Menetrend button with via station and transfer

Given that I navigate to vonatinfo.hu
When I enter Verőce to field labelled Honnan:
And I enter Eger to field labelled Hova:
And I enter Dabas to field labelled Érintve:
And I click on Menetrend button
Then I should see timetable.php page within 2 seconds
And I should see Verőce - Dabas - Eger title in the main title field within 3 seconds

Scenario: check navigation from timetable page back to search page with via station and transfer

Given that I navigate to timetable.php
When I click on Keresés link
Then I should see search.php page within 2 seconds
And I should see Verőce in field labelled Honnan:
And I should see Eger in field labelled Hova:
And I should see Dabas in field labelled Érintve:

Scenario: check navigation from timetable page to trip info page with via station and transfer

Given that I navigate to timetable.php
When I click the 1. Részletek link
Then I should see tripinfo.php page within 2 seconds
And I should see Verőce - Dabas - Eger title in the main title field within 3 seconds

Scenario: check navigation from trip info page back to search page with via station and transfer

Given that I navigate to tripinfo.php
When I click on Keresés link
Then I should see search.php page within 2 seconds
And I should see Verőce in field labelled Honnan:
And I should see Eger in field labelled Hova:
And I should see Dabas in field labelled Érintve:

Scenario: check navigation from trip info page back to timetable page with via station and transfer

Given that I navigate to tripinfo.php
When I click on Eger link
Then I should see timetable.php page within 2 seconds
And I should see Verőce - Dabas - Eger title in the main title field within 3 seconds

Scenario: check navigation from trip info page to official station page with via station and transfer

Given that I navigate to tripinfo.php
When I click the 1. station link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 3 seconds

Scenario: check navigation from trip info page to official train page with via station and transfer

Given that I navigate to tripinfo.php
When I click the 1. train link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/vt page within 3 seconds

Scenario: check navigation from timetable page to official page of the initial station with via station and transfer

Given that I navigate to timetable.php
When I click 1. initial station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 3 seconds

Scenario: check navigation from timetable page to official page of the final station with via station and transfer

Given that I navigate to timetable.php
When I click 1. final station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 3 seconds

Scenario: check navigation from timetable page to return trip timetable page with via station and transfer

Given that I navigate to timetable.php
When I click on Visszaút link
Then I should see timetable.php page within 3 seconds
And I should see Eger - Dabas - Verőce title in the main title field within 3 seconds

!-- Scenarios without via station and transfer

Scenario: check Menetrend button without via station and transfer

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I clear field labelled Érintve:
And I click on Menetrend button
Then I should see timetable.php page within 2 seconds
And I should see BUDAPEST* - Esztergom title in the main title field within 3 seconds

Scenario: check navigation from timetable page back to search page without via station and transfer

Given that I navigate to timetable.php
When I click on Keresés link
Then I should see search.php page within 2 seconds
And I should see BUDAPEST* in field labelled Honnan:
And I should see Esztergom in field labelled Hova:
And I should see nothing in field labelled Érintve:

Scenario: check navigation from timetable page to trip info page without via station and transfer

Given that I navigate to timetable.php
When I click the 1. Részletek link
Then I should see tripinfo.php page within 2 seconds
And I should see BUDAPEST* - Esztergom title in the main title field within 3 seconds

Scenario: check navigation from trip info page back to search page withot via station and transfer

Given that I navigate to tripinfo.php
When I click on Keresés link
Then I should see search.php page within 2 seconds
And I should see BUDAPEST* in field labelled Honnan:
And I should see Esztergom in field labelled Hova:
And I should see nothing in field labelled Érintve:

Scenario: check navigation from trip info page back to timetable page with via station and transfer

Given that I navigate to tripinfo.php
When I click on Esztergom link
Then I should see timetable.php page within 2 seconds
And I should see BUDAPEST* - Esztergom title in the main title field within 3 seconds

Scenario: check navigation from trip info page to official station page without via station and transfer

Given that I navigate to tripinfo.php
When I click the 1. station link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 3 seconds

Scenario: check navigation from trip info page to official train page without via station and transfer

Given that I navigate to tripinfo.php
When I click the 1. train link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/vt page within 3 seconds

Scenario: check navigation from timetable page to official page of the initial station without via station and transfer

Given that I navigate to timetable.php
When I click 1. initial station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 3 seconds

Scenario: check navigation from timetable page to official page of the final station without via station and transfer

Given that I navigate to timetable.php
When I click 1. final station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 3 seconds

!-- Scenarios for favourit with via station and transfer

Scenario: check favourite creation with via station and transfer

Given that I navigate to vonatinfo.hu
When I enter Verőce to field labelled Honnan:
And I enter Eger to field labelled Hova:
And I enter Dabas to field labelled Érintve:
And I select checkbox labelled Kerüljön a kedvencek közé:
And I click on Menetrend button
Then I should see timetable.php page within 2 seconds
And I should see Verőce - Dabas - Eger title in the main title field within 3 seconds

Scenario: check favourite listing with via station and transfer
Given that I navigate to timetable.php
When I click on Keresés link
Then I should see search.php page within 2 seconds
When I click on Ma: Verőce - Dabas - Eger button
Then I should see timetable.php page within 2 seconds
And I should see Verőce - Dabas - Eger title in the main title field within 3 seconds

Scenario: check favourite erasure with via station and transfer
Given that I navigate to search.php
When I click on Törlés button
Then I should see search.php page within 2 seconds
And I should not see Ma: Verőce - Dabas - Eger button
