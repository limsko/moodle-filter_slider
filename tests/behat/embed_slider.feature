@filter @filter_slider @javascript
Feature: Show a slider block in any text
  In order to promote content inside activities
  As a teacher
  I need to embed a slider with the [SLIDER-id] code

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | One      |
      | student2 | Student   | Two      |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the "slider" filter is "on"
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion | title   | effect | speed | autoplay |
      | slider    | Course       | C1        | course-view-*   | side-pre      | Banners | fade   | 0     | 0        |
    And the following "block_slider > slides" exist:
      | block   | title  |
      | Banners | First  |
      | Banners | Second |
    And the following "activities" exist:
      | activity | name         | course | content |
      | page     | Welcome page | C1     | Empty   |
    And the slider block "Banners" is embedded in the "Welcome page" page

  Scenario: Enrolled student browses the embedded slider
    When I am on the "Welcome page" "page activity" page logged in as "student1"
    Then I should see "Text before the slider." in the "region-main" "region"
    And I should see "First" in the "region-main" "region"
    And I should not see "Second" in the "region-main" "region"
    And I click on "Next slide" "button" in the "region-main" "region"
    And I should see "Second" in the "region-main" "region"
    And I should see "Text after the slider." in the "region-main" "region"

  Scenario: The slider is not shown to users without access to its course
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student2 | C2     | student |
    And the following "activities" exist:
      | activity | name        | course | content |
      | page     | Other page  | C2     | Empty   |
    And the slider block "Banners" is embedded in the "Other page" page
    When I am on the "Other page" "page activity" page logged in as "student2"
    Then I should see "Text before the slider." in the "region-main" "region"
    And I should not see "First" in the "region-main" "region"
    And "Next slide" "button" should not exist in the "region-main" "region"
