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
    if (!$values = self::getAllRawEntityFieldValues($entity, $fieldName)) {
      return FALSE;
    }

    return $values->getValue();
  }

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return array|bool
   */
  public static function getReferencedEntitiesByEntityFieldValues(FieldableEntityInterface $entity, $fieldName) {
    if (!$values = self::getAllRawEntityFieldValues($entity, $fieldName)) {
      return FALSE;
    }

    $entities = [];

    foreach ($values as $entityFieldValue) {
      $entities[] = $entityFieldValue->entity;
    }

    return $entities;
  }

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return bool|\Drupal\Core\Entity\FieldableEntityInterface
   */
  public static function getReferencedEntityByField(FieldableEntityInterface $entity, $fieldName) {
    $firstValue = self::getFirstRawEntityFieldValue($entity, $fieldName);

    if (!$firstValue) {
      return FALSE;
    }

    if (!$entity = $firstValue->entity) {
      return FALSE;
    }

    return $entity;
  }

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return bool
   */
  public static function getEntityFieldValue(FieldableEntityInterface $entity, $fieldName) {
    $firstValue = self::getFirstRawEntityFieldValue($entity, $fieldName);

    if (!$firstValue) {
      return FALSE;
    }

    if (!$value = $firstValue->value) {
      return FALSE;
    }

    return $value;
  }

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return bool|\Drupal\Core\TypedData\TypedDataInterface
   */
  public static function getFirstRawEntityFieldValue(FieldableEntityInterface $entity, $fieldName) {
    if (!$entity->hasField($fieldName)) {
      return FALSE;
    }

    if (!$fieldValue = $entity->get($fieldName)) {
      return FALSE;
    }

    if ($fieldValue->isEmpty()) {
      return FALSE;
    }

    if (!$value = $fieldValue->first()) {
      return FALSE;
    }

    return $value;
  }

  /**
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   * @param $fieldName
   *
   * @return bool|\Drupal\Core\Field\FieldItemListInterface
   */
  public static function getAllRawEntityFieldValues(FieldableEntityInterface $entity, $fieldName) {
    if (!$entity->hasField($fieldName)) {
      return FALSE;
    }

    if (!$fieldValue = $entity->get($fieldName)) {
      return FALSE;
    }

    if ($fieldValue->isEmpty()) {
      return FALSE;
    }

    return $fieldValue;
  }

}