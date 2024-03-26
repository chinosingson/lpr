CONTENTS OF THIS FILE
---------------------

* Introduction
* Requirements
* Installation
* Configuration
* Troubleshooting
* Maintainers

VERSION
--------
Current Verson 2.1


INTRODUCTION
------------

The Login Switch is a module that modifies Drupal's core user.login,
user.register, user.password routes by changing the path to a custom path.


REQUIREMENTS
------------

This module requires no modules outside of Drupal core.


INSTALLATION
------------

Recommended installation:  composer require drupal/login_switch.

Install the user login_switch module as you would normally install a contributed
Drupal module.

Visit https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
--------------

There is a configuration page located at /admin/config/login_switch.

The route can be overridden in your settings file using the following:
$config['login_switch.settings']['login_route'] = '';
$config['login_switch.settings']['register_route'] = '';
$config['login_switch.settings']['password_route'] = '';


You can turn on and off the module using the Disable Default User Login Route
checkbox. Checked will override the default route with either /drupal/login
or one custom set by you. Please remember to clear all caches for this
to take effect.

New in 2.1 we added the ability to add a nofollow header for each of the routes.

TROUBLESHOOTING
-----------
If the route is not picked up immediately, clear your Drupal caches either at
/admin/config/development/performance
OR
use the "drush cr" command

MAINTAINERS
-----------

Currently maintained by:
* Matthew Sherman
