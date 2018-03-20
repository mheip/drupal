<?php
/**
 * User: maarten.heip
 * Date: 16/03/2018
 * Time: 10:09
 */

namespace Mheip\Drupal\Helpers\Entities;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;

abstract class EntityHelper {

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   * @param $fieldName
   */
  public static function getEntityFieldValues(EntityInterface $entity, $fieldName) {

  }

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return bool
   */
  public static function getEntityFieldValue(FieldableEntityInterface $entity, $fieldName) {
    if (!$entity->hasField($fieldName)) {
      return FALSE;
    }

    if (!$fieldValue = $entity->get($fieldName)) {
      return FALSE;
    }

    if ($fieldValue->isEmpty()) {
      return FALSE;
    }

    if (!$value = $fieldValue->first()->value) {
      return FALSE;
    }

    return $value;
  }

}