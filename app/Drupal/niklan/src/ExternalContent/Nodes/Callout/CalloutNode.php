<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Nodes\ContentNode;

final class CalloutNode extends ContentNode {

  public function __construct(string $calloutType) {
    parent::__construct();
    $this->setCalloutType($calloutType);
  }

  public function setCalloutType(string $calloutType): void {
    $this->getProperties()->setProperty('calloutType', $calloutType);
  }

  public function getCalloutType(): string {
    return $this->getProperties()->getProperty('calloutType');
  }

  public function addChild(ContentNode $node): void {
    if (!$node instanceof CalloutTitleNode && !$node instanceof CalloutBodyNode) {
      throw new \InvalidArgumentException('Only CalloutTitleNode and CalloutBodyNode can be added as children.');
    }
    parent::addChild($node);
  }

  public function getBody(): ?CalloutBodyNode {
    foreach ($this->children as $child) {
      if ($child instanceof CalloutBodyNode) {
        return $child;
      }
    }
    return NULL;
  }

  public function getTitle(): ?CalloutTitleNode {
    foreach ($this->children as $child) {
      if ($child instanceof CalloutTitleNode) {
        return $child;
      }
    }

    return NULL;
  }

  public static function getType(): string {
    return 'niklan:callout';
  }

}
