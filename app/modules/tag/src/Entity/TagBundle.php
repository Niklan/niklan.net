<?php

declare(strict_types=1);

namespace Drupal\app_tag\Entity;

use Drupal\app_contract\Contract\Tag\Tag;
use Drupal\taxonomy\Entity\Term;

final class TagBundle extends Term implements Tag {

}
