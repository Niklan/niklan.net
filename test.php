<?php

use Drupal\niklan\Entity\File\FileInterface;

$files = \Drupal\niklan\Entity\File\File::loadMultiple();

$mimies = [];

foreach ($files as $file) {
  assert($file instanceof FileInterface);
  $mimies[] = $file->getMimeType();
}

dump(array_unique($mimies));
