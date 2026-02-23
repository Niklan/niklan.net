<?php

declare(strict_types=1);

namespace Drupal\app_contract\Node;

use Drupal\app_contract\Contract\Node\Node;
use Drupal\node\Entity\Node as DrupalNode;

abstract class NodeBundle extends DrupalNode implements Node {

}
