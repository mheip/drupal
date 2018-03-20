<?php

namespace Mheip\Drupal\State\Forms;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Cache\Cache;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class TranslatableStateSettingsForm extends StateSettingsForm {

  /**
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * @var string
   */
  protected $langcode;

  /**
   * Constructs a \Drupal\ds\Form\EmergencyForm object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state key value store.
   * @param \Drupal\Core\Language\LanguageManager $languageManager
   */
  public function __construct(
    StateInterface $state,
    LanguageManager $languageManager
  ) {
    parent::__construct($state);
    $this->languageManager = $languageManager;
    $this->langcode = $languageManager->getCurrentLanguage()->getId();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function storeStateValues(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $values = $form_state->getValues();
    $stateValues = $this->getStateValues();
    $stateValues[$this->langcode] = $values;
    $this->setStateValues($stateValues);
  }

}