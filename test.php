<?php

use Drupal\external_content\Contract\ExternalContentMarkupConverterInterface;
use Drupal\external_content\Data\Configuration;
use Drupal\external_content\Environment\Environment;

$class = '\Drupal\external_content\Converter\ExternalContentMarkupConverter';
dump(is_subclass_of($class, ExternalContentMarkupConverterInterface::class));

$env = new Environment(new Configuration());
