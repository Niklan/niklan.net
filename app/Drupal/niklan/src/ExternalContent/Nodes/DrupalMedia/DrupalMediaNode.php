<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\DrupalMedia;

use Drupal\external_content\Nodes\ContentNode;

final class DrupalMediaNode extends ContentNode {

  public function __construct(private string $uuid, private array $mediaMetadata = []) {
    parent::__construct();
    $this->setUuid($this->uuid);
    foreach ($this->mediaMetadata as $key => $value) {
      $this->getProperties()->setProperty($key, $value);
    }
  }

  public function setUuid(string $uuid): void {
    $this->getProperties()->setProperty('uuid', $uuid);
  }

  public function getUuid(): string {
    return $this->getProperties()->getProperty('uuid');
  }

  public static function getType(): string {
    return 'niklan:drupal_media';
  }

}
