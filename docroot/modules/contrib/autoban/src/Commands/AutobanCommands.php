<?php

namespace Drupal\autoban\Commands;

use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 */
class AutobanCommands extends DrushCommands {

  /**
   * The autoban object.
   *
   * @var \Drupal\autoban\Controller\AutobanController
   */
  protected $autoban;

  /**
   * Autoban IP ban.
   *
   * @param string $rule
   *   Autoban rule id.
   *
   * @command autoban:ban
   * @aliases autoban-ban
   * @usage autoban-ban [rule]
   */
  public function ban($rule = NULL) {
    $this->autoban = \Drupal::service('autoban');
    $autobanStorage = \Drupal::service('entity_type.manager')
      ->getStorage('autoban');
    $this->output()->writeln('Autoban banning start...');

    if (!empty($rule)) {
      // Checking the rule received from the user input.
      if (!$autobanStorage->load($rule)) {
        $this->logger()->error(sprintf('Wrong rule %s', $rule));
        return;
      }

      $this->output()->writeln(sprintf('Ban for rule %s', $rule));
      $banned = $this->banRule($rule);

      if ($banned > 0) {
        $this->logger()->success(sprintf('Banned count: %s', $banned));
      }
      else {
        $this->logger()->warning('No banned IP.');
      }
    }
    else {
      $rules = $autobanStorage->loadMultiple();
      $this->output()->writeln(sprintf('Ban for all rules: %s', count($rules)));
      if (!empty($rules)) {
        $totalBanned = 0;
        foreach (array_keys($rules) as $rule) {
          $banned = $this->banRule($rule);
          $this->logger()->notice(
            sprintf('Rule %s Banned count: %s', $rule, $banned)
          );
          $totalBanned += $banned;
        }

        if ($totalBanned > 0) {
          $this->logger()->success(sprintf('Total banned IP count: %s', $totalBanned));
        }
        else {
          $this->logger()->warning('No banned IP.');
        }
      }
    }

    $this->output()->writeln('Finished.');
  }

  /**
   * Rule ban.
   *
   * @param string $rule
   *   Autoban rule id.
   *
   * @return int
   *   Banned IP count.
   */
  private function banRule($rule) {
    $bannedIp = $this->autoban->getBannedIp($rule);
    $banned = 0;
    if ($bannedIp) {
      $banned = $this->autoban->banIpList($bannedIp, $rule);
    }
    return $banned;
  }

}
