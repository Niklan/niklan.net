<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Plugin;

use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Nodes\Root;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;

interface EnvironmentPlugin {

  public function parse(ImporterSource $source): Root;

  public function denormalize(string $json): Root;

  public function normalize(Root $content): string;

  public function view(Root $content, ViewRequest $request): array;

}
