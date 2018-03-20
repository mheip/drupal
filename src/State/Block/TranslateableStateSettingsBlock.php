<?php
/**
 * Created by PhpStorm.
 * User: maartenheip
 * Date: 20/03/18
 * Time: 22:47
 */

namespace Mheip\Drupal\State\Block;

use Drupal\Core\Language\LanguageManager;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


abstract class TranslateableStateSettingsBlock extends StateSettingsBlock {

  /**
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * @var string
   */
  protected $langcode;

  /**
   * StateSettingsBlock constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\State\StateInterface $state
   * @param \Drupal\Core\Language\LanguageManager $languageManager
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    StateInterface $state,
    LanguageManager $languageManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $state);
    $this->languageManager = $languageManager;
    $this->langcode = $languageManager->getCurrentLanguage()->getId();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $configuration, $plugin_id, $plugin_definition,
      $container->get('state'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getStateValues() {
    $stateValues = parent::getStateValues();

    if (!$stateValues || !isset($stateValues[$this->langcode])) {
      return [];
    }

    return $stateValues[$this->langcode];
  }
}