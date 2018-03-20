<?php
/**
 * Created by PhpStorm.
 * User: maartenheip
 * Date: 20/03/18
 * Time: 22:44
 */

namespace Mheip\Drupal\State\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class StateSettingsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var string
   */
  protected $stateKey = '';

  /**
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * StateSettingsBlock constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\State\StateInterface $state
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    StateInterface $state
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->state = $state;
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
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(["state.cache_tag.{$this->stateKey}"], parent::getCacheTags());
  }
}