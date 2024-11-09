<?php

declare(strict_types=1);

namespace Drupal\niklan\Content\Blog\Entity;

use Drupal\niklan\Content\NodeInterface;

/**
 * @todo Remove
 */
interface BlogEntryInterface extends NodeInterface {

  public function setExternalId(string $external_id): self;

  public function getExternalId(): string;

}
