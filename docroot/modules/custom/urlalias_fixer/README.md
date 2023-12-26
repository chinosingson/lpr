# URL Alias Entity Fixer module

Fixes the Mismatched entity and/or field definitions error (The URL alias entity type needs to be updated.)

## Installation

Install as you would normally install a contributed Drupal module. For further information, see [Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules). Or, on the command line, run 

```drush en urlalias_fixer```.

## Usage

On the command line, run 

```drush php-eval "\Drupal::moduleHandler()->loadInclude('urlalias_fixer', 'install');  urlalias_fixer_update_10000();"```

