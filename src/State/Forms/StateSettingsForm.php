<?php

namespace Mheip\Drupal\State\Forms;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Cache\Cache;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GeneralSettingsForm
 */
abstract class StateSettingsForm extends FormBase implements ContainerInjectionInterface {

  /**
   * @var string
   */
  protected $stateKey = '';

  /**
   * State object.
   *
   * @var \Drupal\Core\State\State
   */
  protected $state;

  /**
   * Constructs a \Drupal\ds\Form\EmergencyForm object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state key value store.
   */
  public function __construct(
    ModuleHandlerInterface $module_handler,
    StateInterface $state
  ) {
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state')
    );
  }

  /**
   * Returns the state contact values.
   *
   * @return array $values.
   *  Array containing state values of contact.
   */
  protected function getStateValues() {
    return $this->state->get($this->stateKey);
  }

  /**
   * Sets the form values in the state object.
   *
   * @param $values
   */
  protected function setStateValues($values) {
    $this->state->set($this->stateKey, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->storeStateValues($form, $form_state);
    $this->invalidateStateCacheTags();
  }

  /**
   * Stores state values from the form state.
   *
   * @param array $form
   *  Array containing form settinsg.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *  Stores the form state.
   */
  protected function storeStateValues(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $values = $form_state->getValues();
    $this->setStateValues($values);
  }

  /**
   * Invalidates cache tags of this state.
   */
  protected function invalidateStateCacheTags() {
    $cacheTag = "state.cache_tag.{$this->stateKey}";
    Cache::invalidateTags([$cacheTag]);
  }

}
