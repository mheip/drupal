<?php
/**
 * User: maarten.heip
 * Date: 16/03/2018
 * Time: 10:28
 */

namespace Mheip\Drupal\Entities;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\NodeInterface;

class ParagraphHelper extends EntityHelper {

  /**
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *
   * @return bool|\Drupal\Core\Entity\ContentEntityInterface|\Drupal\Core\Entity\EntityInterface|\Mheip\Drupal\Entities\ParagraphHelper|null
   */
  public static function getNodeParent(Paragraph $paragraph) {
    return self::getParentOfType($paragraph, Node::class);
  }

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param string $type
   *
   * @return bool
   */
  public static function getParentOfType(EntityInterface $entity, string $type) {
    if (!$entity instanceof Paragraph) {
      return FALSE;
    }

    $parent = $entity->getParentEntity();

    if ($parent instanceof $type) {
      return $parent;
    }

    if ($parent instanceof EntityInterface) {
      return self::getParentOfType($parent, $type);
    }

    return FALSE;
  }

}