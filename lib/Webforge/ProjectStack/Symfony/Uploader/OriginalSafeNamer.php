<?php

namespace Webforge\ProjectStack\Symfony\Uploader;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use URLify;

/**
 * Origname Namer but safe for filesystems
 *
 * @author Philipp Scheit<p.scheit@ps-webforge.com>
 */
class OriginalSafeNamer implements NamerInterface {

  /**
   * {@inheritDoc}
   */
  public function name($object, $field) { //PropertyMapping $mapping
    // for new version:
    //$file = $mapping->getFile($object);

    $refObj = new \ReflectionObject($object);

    $refProp = $refObj->getProperty($field);
    $refProp->setAccessible(true);

    $file = $refProp->getValue($object);
    /** @var $file UploadedFile */

    $extension = URLify::filter($file->getClientOriginalExtension());
    $filename = mb_substr($file->getClientOriginalName(), 0, -(mb_strlen($extension)+1));

    return uniqid().'_'.URLify::filter($filename).'.'.$extension;
  }
}
