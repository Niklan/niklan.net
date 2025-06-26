<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Nodes\RemoteVideo;

use Drupal\external_content\Nodes\Content\Content;

final class RemoteVideo extends Content {

  public function __construct(public string $url) {
    parent::__construct();
    $this->setUrl($url);
  }

  public function setUrl(string $url): void {
    $this->getProperties()->setProperty('url', $url);
  }

  public function getUrl(): string {
    return $this->getProperties()->getProperty('url');
  }

  public static function getType(): string {
    return 'niklan:remote_video';
  }

}
