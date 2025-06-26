<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\Callout;

use Drupal\external_content\Nodes\Content\Content;
use Drupal\niklan\ExternalContent\Nodes\CalloutBody\CalloutBody;
use Drupal\niklan\ExternalContent\Nodes\CalloutTitle\CalloutTitle;

final class CalloutNode extends Content {

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

  public function addChild(Content $node): void {
    if (!$node instanceof CalloutTitle && !$node instanceof CalloutBody) {
      throw new \InvalidArgumentException('Only CalloutTitleNode and CalloutBodyNode can be added as children.');
    }
    parent::addChild($node);
  }

  public function getBody(): ?CalloutBody {
    foreach ($this->children as $child) {
      if ($child instanceof CalloutBody) {
        return $child;
      }
    }
    return NULL;
  }

  public function getTitle(): ?CalloutTitle {
    foreach ($this->children as $child) {
      if ($child instanceof CalloutTitle) {
        return $child;
      }
    }

    return NULL;
  }

  public static function getType(): string {
    return 'niklan:callout';
  }

}
