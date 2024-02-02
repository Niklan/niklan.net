<?php

use Drupal\media\Entity\Media;
use Drupal\niklan\Entity\File\FileInterface;
use Drupal\niklan\Helper\FileHelper;
//
//$files = \Drupal\niklan\Entity\File\File::loadMultiple();
//
//$mimies = [];
//
//foreach ($files as $file) {
//  assert($file instanceof FileInterface);
//  $mimies[] = FileHelper::extension($file->getMimeType());
//}

//dump(array_unique($mimies));

$media = Media::create(['bundle' => 'file']);
dump($media->getSource()->getConfiguration()['source_field']);
