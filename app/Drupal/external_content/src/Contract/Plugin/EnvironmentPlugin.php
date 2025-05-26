<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Plugin;

use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\DataStructure\Nodes\RootNode;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;

interface EnvironmentPlugin {

  public function parse(ImporterSource $source): RootNode;

  public function denormalize(string $json): RootNode;

  public function normalize(RootNode $content): string;

  public function view(RootNode $content, ViewRequest $request): array;

}
