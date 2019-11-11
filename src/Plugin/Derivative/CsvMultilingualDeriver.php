<?php

namespace Drupal\migrate_example\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides language switcher block plugin definitions for all languages.
 */
class CsvMultilingualDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * CsvMultilingualDeriver constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   */
  public function __construct(LanguageManagerInterface $languageManager) {
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {

    $languages = $this->languageManager->getLanguages();
    foreach ($languages as $language) {
      // Skip en.
      if ($language->getId() === 'en') {
        continue;
      }

      $derivative = $this->createAdditionalDerivative($base_plugin_definition, $language);
      $this->derivatives[$language->getId()] = $derivative;
    }

    return $this->derivatives;
  }

  /**
   * Creates a derivative definition for each available language.
   *
   * @param array $base_plugin_definition
   * @param LanguageInterface $language
   *
   * @return array
   */
  private function createAdditionalDerivative(array $base_plugin_definition, LanguageInterface $language) {
    $base_plugin_definition['process']['nid'] = [
      'plugin' => 'migration_lookup',
      'source' => 'id',
      'migration' => 'articles'
    ];

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language->getId(),
    ];

    $base_plugin_definition['process']['content_translation_source'] = [
      'plugin' => 'default_value',
      'default_value' => 'en',
    ];

    return $base_plugin_definition;
  }

}
