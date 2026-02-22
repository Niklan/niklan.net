<?php

declare(strict_types=1);

namespace Drupal\app_contract\Contract\Node;

interface Article extends Node {

  public function setExternalId(string $external_id): self;

  public function getExternalId(): string;

}
