<?php

namespace Mheip\Drupal\State\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
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

  /**
   * {@inheritdoc}
   */
  public function getStateValue(string $key) {
    $stateValues = $this->getStateValuesInLanguage();

    if (!$stateValues || !isset($stateValues[$key])) {
      return FALSE;
    }

    return $stateValues[$key];
  }

  /**
   * Returns state values in a certain language.
   *
   * @return bool
   *  The state values in a certain language.
   */
  public function getStateValuesInLanguage() {
    $values = $this->getStateValues();

    if (empty($values[$this->langcode])) {
      return FALSE;
    }

    return $values[$this->langcode];
  }

  /**
   * Returns a language switcher form element.
   *
   * @param array $form
   *   The form.
   */
  protected function getLanguageSwitcher(array &$form) {
    foreach ($this->languageManager->getLanguages() as $language) {
      $links[] = [
        '#title' => $this->t('Translate @language', ['@language' => $language->getName()]),
        '#type' => 'link',
        '#url' => Url::fromRoute('<current>', [], ['language' => $language]),
      ];
    }

    $form['language_switcher'] = [
      '#theme' => 'item_list',
      '#items' => $links ?? [],
    ];
  }

}