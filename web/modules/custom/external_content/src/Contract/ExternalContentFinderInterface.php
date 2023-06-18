<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract;

use Drupal\external_content\Data\ExternalContentFileCollection;


interface ExternalContentFinderInterface {

  public function find(): ExternalContentFileCollection;

}
