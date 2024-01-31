<?php

use Drupal\niklan\Hook\Deploy\Deploy0003;

$sandbox = [
  '#finished' => 0,
];
while ($sandbox['#finished'] < 1) {
  \Drupal::classResolver(Deploy0003::class)($sandbox);
}
