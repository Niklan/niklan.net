<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\ContainerDirective;

use Drupal\external_content\Nodes\Content\Content;

/**
 * A generic container directive node.
 */
final class ContainerDirective extends Content {

  public function __construct(string $directiveType) {
    parent::__construct();
    $this->setDirectiveType($directiveType);
  }

  public function setDirectiveType(string $directiveType): void {
    $this->getProperties()->setProperty('directiveType', $directiveType);
  }

  public function getDirectiveType(): string {
    return $this->getProperties()->getProperty('directiveType');
  }

  public static function getType(): string {
    return 'niklan:container_directive';
  }

}
