# Allow a content type only once (Only One)

The Allow a content type only once (Only One) module allows the creation of
Only One content per language in the selected content types for this
configuration.

For a full description of the module, visit the
[project page](https://www.drupal.org/project/onlyone).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/search/onlyone).


## Table of contents

- Requirements
- Recommended modules
- Installation
- Configuration
- Maintainers


## Requirements

This module requires no modules outside of Drupal core.


## Recommended modules

Improves the module help page showing information about the module drush
commands.
[Drush Help](https://www.drupal.org/project/drush_help):


## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).


## Configuration


1. Configure the content types in Administration » Configuration »
   Content authoring » Only One:

   - In the 'Available content types for Only One' section check the content
     types that should have Only One content per language. For this you need the
     'Administer Only One' permission.

1. Configure the module settings in Administration » Configuration »
   Content authoring » Only One » Settings:

   - If you want to have the configured content types in a new menu entry named
     'Add content (Only One)' you must check the option 'Show configured content
     types in a new menu entry', the new menu link will be available in 
     Administration » Content, as an action link to the 'Add content (Only One)'
     then the 'Add content' menu link will show the not configured content
     types. For this you need the 'Administer Only One' permission.

1. Creating content:

   - Once you try to Add content in Administration » Content if the chosen
     content type is configured to have Only One content and it already has one
     content created in the actual language, you will be redirected to edit the
     content, otherwise, you will go to create a new one.

1. Drush commands

### drush onlyone-list
    
  - Shows a content types list according to the selected status.

### drush onlyone-enable

  - Enables the 'Only One content' mode on content types.

### drush onlyone-disable

  - Disables the 'Only One content' mode on content types.

### drush onlyone-new-menu-entry

  - Configures if the configured content types will be shown in a new menu entry.


## Maintainers

- Adrian Cid Almaguer - [adriancid](https://www.drupal.org/u/adriancid)
- Pierre Vriens - [Pierre.Vriens](https://www.drupal.org/u/pierrevriens)
- Jorge Diaz - [jorgediazhav)](https://www.drupal.org/u/jorgediazhav)
