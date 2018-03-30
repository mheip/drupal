<?php
/**
 * User: maarten.heip
 * Date: 16/03/2018
 * Time: 10:09
 */

namespace Mheip\Drupal\Entities;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;

abstract class EntityHelper {

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return bool
   */
  public static function getEntityFieldValues(FieldableEntityInterface $entity, $fieldName) {
    if (!$entity->hasField($fieldName)) {
      return FALSE;
    }

    if (!$fieldValue = $entity->get($fieldName)) {
      return FALSE;
    }

    if ($fieldValue->isEmpty()) {
      return FALSE;
    }

    return $fieldValue->getValue();
  }

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return array|bool
   */
  public static function getReferencedEntitiesByEntityFieldValues(FieldableEntityInterface $entity, $fieldName) {
    if (!$entity->hasField($fieldName)) {
      return FALSE;
    }

    if (!$fieldValue = $entity->get($fieldName)) {
      return FALSE;
    }

    if ($fieldValue->isEmpty()) {
      return FALSE;
    }

    $entities = [];

    foreach ($fieldValue as $entityFieldValue) {
      $entities[] = $entityFieldValue->entity;
    }

    return $entities;
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