<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Plugin;

use Drupal\external_content\Contract\Importer\ImporterSource;
use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;

interface EnvironmentPlugin {

  public function parse(ImporterSource $source): Document;

  public function denormalize(string $json): Document;

  public function normalize(Document $content): string;

  public function view(Document $content, ViewRequest $request): array;

}
