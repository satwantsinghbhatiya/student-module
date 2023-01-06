<?php

/**
 * @file
 * Contains student.page.inc.
 *
 * Page callback for Student entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Student templates.
 *
 * Default template: student.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_student(array &$variables) {
  // Fetch Student Entity Object.
  $student = $variables['elements']['#student'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
