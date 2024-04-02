(function(Drupal, drupalSettings) {
  'use strict';

  /**
   * @type {Drupal.insert.Inserter[]}
   */
  const registry = [];

  /**
   * Behavior to add "Insert" buttons.
   */
  Drupal.behaviors.insert = {};
  Drupal.behaviors.insert.attach = function(context) {

    if (!drupalSettings.insert) {
      return;
    }

    Drupal.insert.FocusManager = new Drupal.insert.Manager();

    // Populate the Focus Manager registry
    document.querySelectorAll('.ck-editor__editable').forEach(element => {
      Drupal.insert.FocusManager.addEditor(element.ckeditorInstance);
    });
    document.querySelectorAll('textarea').forEach(textarea => {
      Drupal.insert.FocusManager.addTextarea(textarea);
    });

    // Initialize Inserter managing content insertion
    context.querySelectorAll('.insert').forEach(element => {
      if (
        !element.hasChildNodes()
        || registry.find(inserter => inserter.container === element)
      ) {
        return;
      }

      const insertType = element.dataset.insertType;

      registry.push(
        new Drupal.insert.Inserter(
          element,
          new Drupal.insert[insertType === 'image'
            ? 'ImageHandler'
            : 'FileHandler'
          ](
            element,
            drupalSettings.insert.widgets[insertType]
          )
        ),
      );
    });
  };

})(Drupal, drupalSettings);