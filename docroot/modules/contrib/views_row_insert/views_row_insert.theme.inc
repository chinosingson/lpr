<?php

/**
 * @file
 * Theme for views_row_insert views plugin.
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements template_preprocess_hook().
 */
function template_preprocess_views_row_insert(&$variables) {
  $view = $variables['view'];
  $rows = $variables['rows'];
  $style = $view->style_plugin;
  $options = $style->options;
  $use_plugin = !empty($options['use_plugin']);
  $variables['use_plugin'] = $use_plugin;
  $row_header = !empty($options['row_header']);
  $row_footer = !empty($options['row_footer']);
  $row_limit = !empty($options['row_limit_flag']) ? $options['row_limit'] : FALSE;
  if ($row_limit == 0) {
    $row_limit = FALSE;
  }
  $rows_number = isset($options['rows_number']) ? $options['rows_number'] : 2;
  $default_rows = !empty($options['default_rows']);
  $strip_rows = !empty($options['strip_rows']);
  $row_class = !empty($options['row_class']) ? $options['row_class'] : FALSE;
  $data_mode = !empty($options['data_mode']) ? $options['data_mode'] : FALSE;
  $insert_classes = !empty($options['class_name']) ? $options['class_name'] : '';
  $custom_row_data = !empty($options['custom_row_data']) ? $options['custom_row_data'] : '';
  if ($data_mode == 'vri_block') {
    $definitions = _views_row_insert_get_blocks_definitions();
    $bid = $options['block_name'];
    $block_def = $definitions[$bid];
    if ($block_def['provider'] == 'block_content') {
      $uuid = str_replace('block_content:', '', $bid);
      $block_entity = \Drupal::service('entity.repository')
        ->loadEntityByUuid('block_content', $uuid);
      $block = \Drupal::service('entity.repository')->getTranslationFromContext($block_entity);
      $insertRow = \Drupal::entityTypeManager()->getViewBuilder('block_content')->view($block);
    }
    else {
      $block_manager = \Drupal::service('plugin.manager.block');
      $config = [];
      $plugin_block = $block_manager->createInstance($bid, $config);
      $access_result = $plugin_block->access(\Drupal::currentUser());
      if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
        $insertRow = '';
      }
      else {
        $insertRow = $plugin_block->build();
      }
    }
  }
  else {
    $insertRow = $custom_row_data;
  }
  if (!empty($insert_classes)) {
    $insert_classes = preg_replace('/[^a-zA-Z0-9-_\s]/', '', $insert_classes);
  }

  // Here we start inserting our row.
  if ($use_plugin) {
    $new_rows = [];
    $k = 0;
    $counter = 0;
    $insert_keys = [];
    if ($row_header && !empty($insertRow)) {
      $new_rows[] = $insertRow;
      $insert_keys[] = _views_row_insert_last_key($new_rows);
      if ($row_limit) {
        $counter++;
      }
    }
    foreach ($rows as $id => $row) {
      $k++;
      $new_rows[] = $row;
      if ($k == $rows_number) {
        if (empty($row_limit) && !empty($insertRow)) {
          $new_rows[] = $insertRow;
          $insert_keys[] = _views_row_insert_last_key($new_rows);
        }
        elseif ($row_limit > $counter && !empty($insertRow)) {
          $new_rows[] = $insertRow;
          $insert_keys[] = _views_row_insert_last_key($new_rows);
          $counter++;
        }
        $k = 0;
      }
    }
    if ($row_footer && $k > 0) {
      if (empty($row_limit) && !empty($insertRow)) {
        $new_rows[] = $insertRow;
        $insert_keys[] = _views_row_insert_last_key($new_rows);
      }
      elseif ($row_limit > $counter && !empty($insertRow)) {
        $new_rows[] = $insertRow;
        $insert_keys[] = _views_row_insert_last_key($new_rows);
      }
    }
    $count = 0;
    $max = count($new_rows);
    foreach ($new_rows as $id => $row) {
      $classes = [];
      $insert_class = FALSE;
      $count++;
      $variables['rows_insert'][$id] = [];
      $variables['rows_insert'][$id]['content'] = $row;
      if ($default_rows) {
        $classes[] = 'views-row';
        $classes[] = 'views-row-' . $count;
      }
      if ($strip_rows) {
        $classes[] = 'views-row-' . ($count % 2 ? 'odd' : 'even');
        if ($count == 1) {
          $classes[] = 'views-row-first';
        }
        if ($count == $max) {
          $classes[] = 'views-row-last';
        }
      }
      if (in_array($id, $insert_keys)) {
        $classes[] = $insert_classes;
        $insert_class = TRUE;
      }
      elseif ($row_class) {
        $classes[] = $row_class;
      }
      if (($default_rows) || ($strip_rows) || ($row_class) || ($insert_class)) {
        $variables['rows_insert'][$id]['attributes'] = new Attribute();
        $variables['rows_insert'][$id]['attributes']->addClass($classes);
      }
    }
  }
  else {
    foreach ($rows as $id => $row) {
      $variables['rows'][$id] = [];
      $variables['rows'][$id]['content'] = $row;
    }
  }
}