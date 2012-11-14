Story: testing navigation (buttons and links) with as few and simple steps as possible
License: http://www.vonatinfo.hu/license/license.txt

Scenario: check that application version is the same as expected to be tested
 
Given that I navigate to vonatinfo.hu/version.php
Then I should see the traininfo version under testing

Scenario: check Menetrend button

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
Then I should see timetable.php page within 2 seconds
And I should see BUDAPEST* - Esztergom title in the main title field within 3 seconds

Scenario: check navigation from timetable page back to search page

Given that I navigate to vonatinfo.hu
When I enter Verőce to field labelled Honnan:
And I enter Eger to field labelled Hova:
And I enter Dabas to field labelled Érintve:
And I click on Menetrend button
And timetable.php appeared
And I click on Új keresés link
Then I should see search.php page within 2 seconds
And I should see Verőce in field labelled Honnan:
And I should see Eger in field labelled Hova:
And I should see Dabas in field labelled Érintve:

Scenario: check navigation from timetable page to trip info page

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I clear field labelled Érintve:
And I click on Menetrend button
And timetable.php appeared
And I click the 1. Részletek link
Then I should see tripinfo.php page within 2 seconds
And I should see BUDAPEST* - Esztergom title in the main title field within 3 seconds

Scenario: check navigation from trip info page back to search page

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click the 1. Részletek link
And tripinfo.php appeared
And I click on Új keresés link
Then I should see search.php page within 2 seconds
And I should see BUDAPEST* in field labelled Honnan:
And I should see Esztergom in field labelled Hova:

Scenario: check navigation from trip info page back to timetable page

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click the 1. Részletek link
And tripinfo.php appeared
And I click on Vissza az utak listájához link
Then I should see timetable.php page within 2 seconds

Scenario: check navigation from trip info page to official station page

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click the 1. Részletek link
And tripinfo.php appeared
And I click the 1. station link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 2 seconds

Scenario: check navigation from trip info page to official train page

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click the 1. Részletek link
And tripinfo.php appeared
And I click the 1. train link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/vt page within 2 seconds

Scenario: check navigation from timetable page to official page of the initial station without transfer

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click 1. initial station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 2 seconds

Scenario: check navigation from timetable page to official page of the final station without transfer

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click 1. final station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 2 seconds

Scenario: check navigation from timetable page to official train page without transfer

Given that I navigate to vonatinfo.hu
When I enter BUDAPEST* to field labelled Honnan:
And I enter Esztergom to field labelled Hova:
And I click on Menetrend button
And timetable.php appeared
And I click 1. train of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/vt page within 2 seconds

Scenario: check navigation from timetable page to official page of the initial station with transfer

Given that I navigate to vonatinfo.hu
When I enter Verőce to field labelled Honnan:
And I enter Eger to field labelled Hova:
And I enter Dabas to field labelled Érintve:
And I click on Menetrend button
And timetable.php appeared
And I click 1. initial station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 2 seconds

Scenario: check navigation from timetable page to official page of the final station with transfer

Given that I navigate to vonatinfo.hu
When I enter Verőce to field labelled Honnan:
And I enter Eger to field labelled Hova:
And I enter Dabas to field labelled Érintve:
And I click on Menetrend button
And timetable.php appeared
And I click 1. final station of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/af page within 2 seconds

Scenario: check navigation from timetable page to official train page with transfer

Given that I navigate to vonatinfo.hu
When I enter Verőce to field labelled Honnan:
And I enter Eger to field labelled Hova:
And I enter Dabas to field labelled Érintve:
And I click on Menetrend button
And timetable.php appeared
And I click 1. train of the 1. trip link
Then I should see http://elvira.mav-start.hu/elvira.dll/xslvzs/vt page within 2 seconds