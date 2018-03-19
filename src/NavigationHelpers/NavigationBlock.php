<?php
/**
 * Created by PhpStorm.
 * User: maartenheip
 * Date: 19/03/18
 * Time: 21:20
 */

namespace Mheip\Drupal\Helpers\Navigation;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NavigationBlock.
 *
 * @package Mheip\Drupal\Helpers\Navigation
 */
abstract class NavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

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

  protected function getMenuItemData($tree) {
    foreach ($tree as $branch) {

      if (!isset($branch->link)) {
        continue;
      }

      $menuLink = [];
      $link = $branch->link;
      $menuLink['name'] = $link->getTitle();
      $menuLink['url'] = $link->getUrlObject()->toString();
      $menuLink['active'] = $branch->inActiveTrail;

      foreach ($branch->subtree as $child) {

        if (!isset($child->link)) {
          continue;
        }

        $childlink = $child->link;
        $childlinkId = $childlink->getPluginId();

        if (!isset($childlinkId)) {
          continue;
        }

        $menuLink['children'][$childlinkId]['name'] = $childlink->getTitle();
        $menuLink['children'][$childlinkId]['url'] = $childlink->getUrlObject()->toString();
        $menuLink['children'][$childlinkId]['active'] = $child->inActiveTrail;
      }

      $menuItems[] = $menuLink;
    }
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

    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkNodeAccess'],
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    $tree = $this->menuLinkTree->transform($tree, $manipulators);
    return $tree;
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

}
