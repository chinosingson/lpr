/**
 * @file
 * Autoban rules form behaviors.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.autobanForm = {
    attach: function () {

      // Set Type field value from description item.
      $('#edit-type--description span').click(function () {
        var text = $(this).text();
        $('#edit-type').val(text);
      });

      // Set Type field value from description item.
      $('#edit-autoban-window-default--description span').click(function () {
        var text = $(this).text();
        $('#edit-autoban-window-default').val(text);
      });

    }
  };

}(jQuery, Drupal));
