<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\MediaReference;

use Drupal\external_content\Nodes\Content\Content;

final class MediaReference extends Content {

  public function __construct(private readonly string $uuid, private readonly array $mediaMetadata = []) {
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
    return 'niklan:media_reference';
  }

}
