<?php

namespace Drupal\asentech_entities;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Score entity.
 *
 * @see \Drupal\asentech_entities\Entity\Score.
 */
class ScoreAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\asentech_entities\Entity\ScoreInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished score entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published score entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit score entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete score entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add score entities');
  }


}
