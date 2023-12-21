<?php

namespace Drupal\formatter_suite\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\TimestampAgoFormatter;

/**
 * Formats multiple timestamps interpreted as time ago as a list.
 *
 * See the EntityListTrait for a description of list formatting features.
 *
 * @ingroup formatter_suite
 *
 * @FieldFormatter(
 *   id          = "formatter_suite_timestamp_time_ago_list",
 *   label       = @Translation("Formatter Suite - Time ago list"),
 *   weight      = 1000,
 *   field_types = {
 *     "timestamp",
 *     "created",
 *     "changed",
 *   }
 * )
 */
class TimestampTimeAgoListFormatter extends TimestampAgoFormatter {
  use EntityListTrait;

  /*---------------------------------------------------------------------
   *
   * Settings form.
   *
   *---------------------------------------------------------------------*/
  /**
   * Returns a brief description of the formatter.
   *
   * @return string
   *   Returns a brief translated description of the formatter.
   */
  protected function getDescription() {
    return $this->t("Format multi-value timestamp fields as a list. Values are used to calculate a time period between the current date and the field's date. The time period is presented with a selected granularity.");
  }

  /**
   * Post-processes the settings form after it has been built.
   *
   * @param array $elements
   *   The form's elements.
   */
  protected function postProcessSettingsForm(array $elements) {
    // The Drupal core TimestampAgoFormatter creates textfields for the time
    // formats, but doesn't set the textfield's size. This makes it hard to lay
    // it out in the formatter's UI. So, give it a small size. CSS then widens
    // it to the width of the UI.
    if (isset($elements['future_format']) === TRUE) {
      $elements['future_format']['#size']                         = 10;
      $elements['future_format']['#attributes']['size']           = 10;
      $elements['future_format']['#attributes']['spellcheck']     = FALSE;
      $elements['future_format']['#attributes']['autocomplete']   = 'off';
      $elements['future_format']['#attributes']['autocapitalize'] = 'none';
      $elements['future_format']['#attributes']['autocorrect']    = 'off';
    }
    if (isset($elements['past_format']) === TRUE) {
      $elements['past_format']['#size']                         = 10;
      $elements['past_format']['#attributes']['size']           = 10;
      $elements['past_format']['#attributes']['spellcheck']     = FALSE;
      $elements['past_format']['#attributes']['autocomplete']   = 'off';
      $elements['past_format']['#attributes']['autocapitalize'] = 'none';
      $elements['past_format']['#attributes']['autocorrect']    = 'off';
    }

    return $elements;
  }

}
