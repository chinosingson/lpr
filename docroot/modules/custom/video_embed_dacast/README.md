CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 

INTRODUCTION
------------

This module provides [Dacast](https://www.dacast.com) integration with
[Video Embed Field](https://www.drupal.org/project/video_embed_field).

Dacast does not currently support oEmbed.


REQUIREMENTS
------------

 * [Video Embed Field](https://www.drupal.org/project/video_embed_field)


INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

The module currently has no menu or modifiable settings through UI. When
enabled, you will be able to use a **video_embed_field** field to embed Dacast
videos.

The domain used for your Dacast videos can be set in settings, e.g.:

`$config['video_embed_dacast.settings']['domain'] = 'videos.example.com';`

It will default to the standard 'iframe.dacast.com'. This will be configurable
via the UI in a future release.

Example Dacast URL format: https://iframe.dacast.com/b/123456/f/789789
