<?php

namespace Drupal\asentech_entities\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Score entities.
 *
 * @ingroup asentech_entities
 */
interface ScoreInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Score name.
   *
   * @return string
   *   Name of the Score.
   */
  public function getName();

  /**
   * Sets the Score name.
   *
   * @param string $name
   *   The Score name.
   *
   * @return \Drupal\asentech_entities\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setName($name);

  /**
   * Gets the Score creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Score.
   */
  public function getCreatedTime();

  /**
   * Sets the Score creation timestamp.
   *
   * @param int $timestamp
   *   The Score creation timestamp.
   *
   * @return \Drupal\asentech_entities\Entity\ScoreInterface
   *   The called Score entity.
   */
  public function setCreatedTime($timestamp);

}
