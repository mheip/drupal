<?php

namespace Mheip\Drupal\Menu\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Template\Attribute;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MenuBlock.
 *
 * @package Mheip\Drupal\Helpers\Menu
 */
abstract class MenuBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The name of the menu.
   *
   * @var string
   *   Protected variable menuName.
   */
  protected $menuName;

  /**
   * MenuLinkTree.
   *
   * @var \Drupal\Core\Menu\MenuLinkTree
   *   Protected variable menuLinkTree.
   */
  protected $menuLinkTree;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MenuLinkTree $menuLinkTree
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->menuLinkTree = $menuLinkTree;
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
      $container->get('menu.link_tree')
    );
  }

  /**
   * Gets the menu items for the menu.
   */
  protected function getMenuItems() {
    $menuItems = [];
    $tree = $this->getTree();

    if (empty($tree)) {
      return $menuItems;
    }

    return $this->getMenuItemData($tree);
  }

  /**
   * @param array $tree
   *
   * @return array
   */
  protected function getMenuItemData(array $tree) {
    $menuItems = [];

    foreach ($tree as $branch) {
      if (!isset($branch->link)) {
        continue;
      }

      $link = $branch->link;
      $menuLink = [];
      $menuLink['enabled'] = $link->isEnabled();
      $menuLink['name'] = $link->getTitle();
      $menuLink['url'] = $link->getUrlObject()->toString();

      // Create the attributes
      $attributes = $link->getPluginDefinition()['options']['attributes'] ?? [];
      $menuLink['attributes'] = new Attribute($attributes);

      if ($this->inActiveTrail($link->getPluginId())) {
        $menuLink['active'] = TRUE;
        $menuLink['attributes']->addClass('active');
      }

      if (!empty($branch->subtree)) {
        $menuLink['children'] = $this->getMenuItemData($branch->subtree);
      }

      $menuItems[] = $menuLink;
    }

    return $menuItems;
  }

  /**
   * Returns the menu tree.
   *
   * @return array|\Drupal\Core\Menu\MenuLinkTreeElement[]|mixed
   *   Return array with the menu links tree.
   */
  protected function getTree() {
    $parameters = $this->getMenuTreeParameters();
    $tree = $this->menuLinkTree->load($this->menuName, $parameters);
    $tree = $this->menuLinkTree->transform($tree, $this->getManipulators());
    return $tree;
  }

  /**
   * Returns a list of default manipulators.
   *
   * @return array $manipulators
   *   An array of menu manipulators.
   */
  protected function getManipulators() {
    return [
      ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
  }

  /**
   * Returns menu tree parameter object.
   *
   * @param string $root
   *   The root.
   * @param int $maxDepth
   *   The max depth of the tree.
   * @param bool $excludeRoot
   *   Wether or not to include root.
   *
   * @return \Drupal\Core\Menu\MenuTreeParameters
   *   The tree parameters.
   */
  protected function getMenuTreeParameters($root = '', $maxDepth = 2, $excludeRoot = TRUE) {
    $menu_parameters = new MenuTreeParameters();
    $menu_parameters->setMaxDepth($maxDepth);
    $menu_parameters->setRoot($root);

    if ($excludeRoot) {
      $menu_parameters->excludeRoot();
    }

    return $menu_parameters;
  }

  /**
   * Returns cacheContext.
   *
   * @return string[]
   *   Returns string with the cache context.
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(['url'], parent::getCacheContexts());
  }

  /**
   * @param $pluginId
   *
   * @return bool
   */
  public function inActiveTrail($pluginId) {
    $activeTrail = \Drupal::service('menu.active_trail');
    $activeLinks = $activeTrail->getActiveTrailIds($this->menuName);

    if (empty($activeLinks)) {
      return FALSE;
    }

    return in_array($pluginId, array_keys($activeLinks));
  }
}
