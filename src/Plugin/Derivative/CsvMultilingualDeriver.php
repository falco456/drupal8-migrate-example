<?php

namespace Drupal\migrate_api_example\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CSV multilangual deriver.
 */
class CsvMultilingualDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * CategoriesLanguageDeriver constructor.
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
      $new_source_path = \str_replace('en', $language->getId(), $base_plugin_definition['source']['path']);
      // We skip EN as that is the original language and the source file needs
      // to exist.
      if ($language->getId() === 'en' && !\file_exists($new_source_path)) {
        continue;
      }

      $derivative = $this->getDerivativeValues($base_plugin_definition, $language);
      $this->derivatives[$language->getId()] = $derivative;
    }

    return $this->derivatives;
  }

  /**
   * Creates a derivative definition for each available language.
   *
   * @param array|\Drupal\Component\Plugin\Definition\PluginDefinitionInterface $base_plugin_definition
   *   The definition of the base plugin from the derivative plugin.
   * @param LanguageInterface $language
   *   A language object.
   *
   * @return array
   */
  private function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language) {
    $base_plugin_definition['source']['path'] = \str_replace('en', $language->getId(), $base_plugin_definition['source']['path']);

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language->getId(),
    ];

    return $base_plugin_definition;
  }

}
