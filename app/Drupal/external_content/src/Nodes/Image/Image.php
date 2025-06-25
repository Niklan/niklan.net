<?php

declare(strict_types=1);

namespace Drupal\external_content\Nodes\Image;

use Drupal\external_content\Nodes\Content\Content;

final class Image extends Content {

  public const string TYPE = 'image';

  public function __construct(
    string $src,
    string $alt,
  ) {
    parent::__construct();
    $this->setSrc($src);
    $this->setAlt($alt);
  }

  public function setSrc(string $src): void {
    $this->getProperties()->setProperty('src', $src);
  }

  public function getSrc(): string {
    return $this->getProperties()->getProperty('src');
  }

  public function setAlt(string $alt): void {
    $this->getProperties()->setProperty('alt', $alt);
  }

  public function getAlt(): string {
    return $this->getProperties()->getProperty('alt');
  }

  public static function getType(): string {
    return 'image';
  }

}
