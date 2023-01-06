<?php

namespace Drupal\asentech_entities\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Student entities.
 *
 * @ingroup asentech_entities
 */
interface StudentInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Student name.
   *
   * @return string
   *   Name of the Student.
   */
  public function getName();

  /**
   * Sets the Student name.
   *
   * @param string $name
   *   The Student name.
   *
   * @return \Drupal\asentech_entities\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setName($name);

  /**
   * Gets the Student creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Student.
   */
  public function getCreatedTime();

  /**
   * Sets the Student creation timestamp.
   *
   * @param int $timestamp
   *   The Student creation timestamp.
   *
   * @return \Drupal\asentech_entities\Entity\StudentInterface
   *   The called Student entity.
   */
  public function setCreatedTime($timestamp);

}
