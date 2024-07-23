<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Parser;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Node\NodeList;

interface ChildHtmlParserInterface extends EnvironmentAwareInterface {

  public function parse(\DOMNodeList $children): NodeList;

}
