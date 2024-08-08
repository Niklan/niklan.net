<?php

use Drupal\Core\Site\Settings;

$git = \Drupal::service('niklan.process.git');
$a = $git->describeTags(Settings::get('external_content_directory'));
$a->run();
dump($a->getOutput());
dump($a->isSuccessful());
