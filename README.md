CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Installation
* Configuration
* Maintainers


INTRODUCTION
------------

With Smart Content Paragraphs a new paragraph type is created
using Smart Content Segments in order to display conditional variations.

Sample segments:

* A “Spanish on the Go” Segment might target visitors on mobile devices,
  with their browser language set to Spanish and will display
  a certain variation for that condition, while displaying
  a default variation for all the other cases.

* A “Desktop Mac” Segment might target visitors who are not on mobile devices
  and are viewing the site on a computer running Mac OS.


REQUIREMENTS
------------

This module requires the following modules:

* Smart Content (https://www.drupal.org/project/smart_content)

* Smart Content Segments (https://www.drupal.org/project/smart_content_segments)


INSTALLATION
------------
* See this issue for Smart Content Segments and apply patch if required:
  https://www.drupal.org/project/smart_content_segments/issues/3128239

* Install as you would normally install a contributed Drupal module. Visit:
  https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules
  for further information.


CONFIGURATION
-------------

* Add a Smart Segment

  - Navigate to Structure » Smart segment in the administration toolbar.
    This page will list all Smart Segments that have been added.
    If none have been created, you will see
    the message "There are no Smart Segement entities yet."

  - Click the "Add Smart segment" button to add a new Smart Segment.

  - Create all of the conditions you'll need within
    the Smart Segment and click "Save."

* Once a Smart Segment has been saved, it will be available to use in the
  conditions section of any Smart Content Variation Set.
