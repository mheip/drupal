<?php
/**
 * User: maarten.heip
 * Date: 16/03/2018
 * Time: 10:28
 */

namespace Mheip\Drupal\Helpers\Entities;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;

class ParagraphHelper extends EntityHelper {

  /**
   * @param \Drupal\Core\Entity\EntityInterface $paragraph
   *
   * @return bool
   */
  public static function getNodeParent(EntityInterface $paragraph) {
    $parent = $paragraph->getParentEntity();

    if ($parent instanceof NodeInterface) {
      return $parent;
    }

    if ($parent instanceof EntityInterface) {
      return self::getNodeParent($parent);
    }

    return FALSE;
  }

}