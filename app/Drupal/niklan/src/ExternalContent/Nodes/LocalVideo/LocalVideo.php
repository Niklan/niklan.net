<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\LocalVideo;

use Drupal\external_content\Nodes\Content\Content;

final class LocalVideo extends Content {

  public function __construct(string $src, string $title) {
    parent::__construct();
    $this->setSrc($src);
    $this->setTitle($title);
  }

  public function setSrc(string $src): void {
    $this->getProperties()->setProperty('src', $src);
  }

  public function getSrc(): string {
    return $this->getProperties()->getProperty('src');
  }

  public function setTitle(string $title): void {
    $this->getProperties()->setProperty('title', $title);
  }

  public function getTitle(): string {
    return $this->getProperties()->getProperty('title');
  }

  public static function getType(): string {
    return 'niklan:local_video';
  }

}
