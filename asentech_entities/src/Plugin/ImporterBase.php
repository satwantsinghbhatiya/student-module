<?php

namespace Drupal\asentech_entities\Plugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Component\Utility\Unicode;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for ImporterBase plugins.
 *
 * @see \Drupal\asentech_entities\Annotation\Importer
 * @see \Drupal\asentech_entities\Plugin\ImporterManager
 * @see \Drupal\asentech_entities\Plugin\ImporterInterface
 * @see plugin_api
 */
abstract class ImporterBase extends PluginBase implements ImporterInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs ImporterBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function data() {
    $csv = $this->configuration['csv'];
    $return = [];

    if ($csv && is_array($csv)) {
      $csv_fields = $csv[0];
      unset($csv[0]);
      foreach ($csv as $index => $data) {
        foreach ($data as $key => $content) {
          if (isset($csv_fields[$key])) {
            $content = Unicode::convertToUtf8($content, mb_detect_encoding($content));
            $fields = explode('|', $csv_fields[$key]);

            $field = $fields[0];
            if (count($fields) > 1) {
              foreach ($fields as $key => $in) {
                $return['content'][$index][$field][$in] = $content;
              }
            }
            elseif (isset($return['content'][$index][$field])) {
              $prev = $return['content'][$index][$field];
              $return['content'][$index][$field] = [];

              if (is_array($prev)) {
                $prev[] = $content;
                $return['content'][$index][$field] = $prev;
              }
              else {
                $return['content'][$index][$field][] = $prev;
                $return['content'][$index][$field][] = $content;
              }
            }
            else {
              $return['content'][$index][current($fields)] = $content;
            }
          }
        }

        if (isset($return[$index])) {
          $return['content'][$index] = array_intersect_key($return[$index], array_flip($this->configuration['fields']));
        }
      }
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function add($contents, array &$context) {
    if (!$contents) {
      return NULL;
    }

    if (!isset($context['sandbox']['progress'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['max'] = count($contents);
    }

    $context['sandbox']['progress']++;
    $context['message'] = t('Import entity %index out of %max', ['%index' => $context['sandbox']['progress'], '%max' => $context['sandbox']['max']]);

    $entity_type = $this->configuration['entity_type'];
    $entity_type_bundle = $this->configuration['entity_type_bundle'];
    $entity_definition = $this->entityTypeManager->getDefinition($entity_type);

    $added = 0;
    $updated = 0;

    $content = $contents[$context['sandbox']['progress']];

    if ($entity_definition->hasKey('bundle') && $entity_type_bundle) {
      $content[$entity_definition->getKey('bundle')] = $this->configuration['entity_type_bundle'];
    }

    foreach ($content as $key => $item) {
      if (is_string($item) && file_exists($item)) {
        $created = file_save_data(file_get_contents($item), $this->config->get('system.file')->get('default_scheme') . '://' . basename($item), FileSystemInterface::EXISTS_REPLACE);
        $content[$key] = $created->id();
      }
    }

    /** @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage $entity_storage  */
    $entity_storage = $this->entityTypeManager->getStorage($this->configuration['entity_type']);

    try {
      if (isset($content[$entity_definition->getKeys()['id']]) && $entity = $entity_storage->load($content[$entity_definition->getKeys()['id']])) {
        /** @var \Drupal\Core\Entity\ContentEntityInterface $entity  */
        foreach ($content as $id => $set) {
          $entity->set($id, $set);
        }

        if ($entity->save()) {
          $context['results']['updated'][] = $entity->id();
        }
      }
      else {
        /** @var \Drupal\Core\Entity\ContentEntityInterface $entity  */
        $entity = $this->entityTypeManager->getStorage($this->configuration['entity_type'])->create($content);

        if ($entity->save()) {
          $context['results']['added'][] = $entity->id();
        }
      }
    }
    catch (\Exception $e) {
    }

    if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
      $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function finished($success, array $results, array $operations) {
    $message = '';

    if ($success) {
      $message = $this->t('@count_added content added and @count_updated updated', ['@count_added' => isset($results['added']) ? count($results['added']) : 0, '@count_updated' => isset($results['updated']) ? count($results['updated']) : 0]);
    }

    $this->messenger()->addMessage($message);
  }

  /**
   * {@inheritdoc}
   */
  public function process() {
    if ($data = $this->data()) {
      $process['operations'][] = [
        [$this, 'add'],
        [$data['content']],
      ];
    }

    $process['finished'] = [$this, 'finished'];
    batch_set($process);
  }

}