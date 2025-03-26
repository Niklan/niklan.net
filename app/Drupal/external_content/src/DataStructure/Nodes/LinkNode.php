<?php

declare(strict_types=1);

namespace Drupal\external_content\DataStructure\Nodes;

final class LinkNode extends ElementNode {

  public function __construct(
    string $url,
    ?string $target = NULL,
    ?string $rel = NULL,
    ?string $title = NULL,
  ) {
    parent::__construct();
    $this->setUrl($url);
    $this->setTarget($target);
    $this->setRel($rel);
    $this->setTitle($title);
  }

  public function setUrl(string $url): void {
    $this->getProperties()->setProperty('url', $url);
  }

  public function getUrl(): string {
    return $this->getProperties()->getProperty('url');
  }

  public function setTarget(?string $target): void {
    $target
        ? $this->getProperties()->setProperty('target', $target)
        : $this->getProperties()->removeProperty('target');
  }

  public function getTarget(): ?string {
    return $this->getProperties()->hasProperty('target')
        ? $this->getProperties()->getProperty('target')
        : NULL;
  }

  public function setRel(?string $rel): void {
    $rel
        ? $this->getProperties()->setProperty('rel', $rel)
        : $this->getProperties()->removeProperty('rel');
  }

  public function getRel(): ?string {
    return $this->getProperties()->hasProperty('rel')
        ? $this->getProperties()->getProperty('rel')
        : NULL;
  }

  public function setTitle(?string $title): void {
    $title
        ? $this->getProperties()->setProperty('title', $title)
        : $this->getProperties()->removeProperty('title');
  }

  public function getTitle(): ?string {
    return $this->getProperties()->hasProperty('title')
        ? $this->getProperties()->getProperty('title')
        : NULL;
  }

  public static function getType(): string {
    return 'link';
  }

}
