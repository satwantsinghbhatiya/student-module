<?php

namespace Drupal\asentech_entities\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Student entity.
 *
 * @ingroup asentech_entities
 *
 * @ContentEntityType(
 *   id = "student",
 *   label = @Translation("Student"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\asentech_entities\StudentListBuilder",
 *     "views_data" = "Drupal\asentech_entities\Entity\StudentViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\asentech_entities\Form\StudentForm",
 *       "add" = "Drupal\asentech_entities\Form\StudentForm",
 *       "edit" = "Drupal\asentech_entities\Form\StudentForm",
 *       "delete" = "Drupal\asentech_entities\Form\StudentDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\asentech_entities\StudentHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\asentech_entities\StudentAccessControlHandler",
 *   },
 *   base_table = "student",
 *   translatable = FALSE,
 *   admin_permission = "administer student entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/student/{student}",
 *     "add-form" = "/admin/structure/student/add",
 *     "edit-form" = "/admin/structure/student/{student}/edit",
 *     "delete-form" = "/admin/structure/student/{student}/delete",
 *     "collection" = "/admin/structure/student",
 *   },
 *   field_ui_base_route = "student.settings"
 * )
 */
class Student extends ContentEntityBase implements StudentInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Student entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Student.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Student is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

  
    $fields['class'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Class'))
    ->setDescription(t('Class of student'))
    ->setDisplayOptions('form', array(
      'type' => 'string_textfield',
      'settings' => array(
        'display_label' => TRUE,
      ),
    ))
    ->setDisplayOptions('view', array(
      'label' => 'hidden',
      'type' => 'string',
    ))
    ->setDisplayConfigurable('form', TRUE)
    ->setRequired(TRUE);

    $fields['contact_number'] = BaseFieldDefinition::create('string')
    ->setLabel(t('Contact Number'))
    ->setDescription(t('Contact number'))
    ->setDisplayOptions('form', array(
      'type' => 'string_textfield',
      'settings' => array(
        'display_label' => TRUE,
      ),
    ))
    ->setDisplayOptions('view', array(
      'label' => 'hidden',
      'type' => 'string',
    ))
    ->setDisplayConfigurable('form', TRUE)
    ->setRequired(TRUE);

    return $fields;
  }

}
