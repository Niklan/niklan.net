<?php

declare(strict_types=1);

namespace Drupal\niklan\Entity\Node;

interface BlogEntryInterface extends NodeInterface {

  public function setExternalId(string $external_id): self;

  public function getExternalId(): string;

}
