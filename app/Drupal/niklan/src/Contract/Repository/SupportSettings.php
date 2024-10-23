<?php

declare(strict_types=1);

namespace Drupal\niklan\Contract\Repository;

interface SupportSettings {

  public const string TEXT_FORMAT = 'text';

  public function setBody(string $body): self;

  public function getBody(): string;

  public function setDonateUrl(string $url): self;

  public function getDonateUrl(): string;

}
