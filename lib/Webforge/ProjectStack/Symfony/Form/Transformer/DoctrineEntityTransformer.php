<?php

namespace Webforge\ProjectStack\Symfony\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class DoctrineEntityTransformer implements DataTransformerInterface  {

  /**
   * @var ObjectManager
   */
  protected $om;

  /**
   * @var string
   */
  protected $identifierName;

  /**
   * @param ObjectManager $om
   */
  public function __construct($entityClass, ObjectManager $om, $identifierName = 'id') {
    $this->entityClass = $entityClass;
    $this->identifierName = $identifierName;
    $this->om = $om;
  }

  /**
   * Transforms an entity to its json object
   *
   */
  public function transform($entity) {
    if ($entity === NULL) {
      return NULL;
    }

    $getter = 'get'.ucfirst($this->identifierName);

    return array(
      '__class' => $this->entityClass, // just for verbose
      $this->identifierName => $entity->$getter()
    );
  }

  /**
   * Transforms an json object to an entity
   *
   */
  public function reverseTransform($properties) {
    if (!is_array($properties)) {
      return NULL;
    }

    if (!isset($properties[$this->identifierName])) {
      throw new TransformationFailedException(sprintf(
        'Identifier (%s) for entity %s is not set in properties %s',
        $this->identifierName,
        $this->entityClass,
        json_encode($properties, JSON_PRETTY_PRINT)
      ));
    }

    $identifier = $properties[$this->identifierName];

    $entity = $this->om
      ->getRepository($this->entityClass)
      ->findOneBy(array($this->identifierName => $identifier))
    ;

    if (!($entity instanceof $this->entityClass)) {
      throw new TransformationFailedException(sprintf(
        'Entity %s with identifier (%s) "%s" cannot be found',
        $this->entityClass,
        $this->identifierName,
        $identifier
      ));
    }

    return $entity;
  }
}
