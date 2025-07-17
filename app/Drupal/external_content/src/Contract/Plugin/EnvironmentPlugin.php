<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Plugin;

use Drupal\external_content\Nodes\Document;
use Drupal\external_content\Plugin\ExternalContent\Environment\ViewRequest;

/**
 * @template TParseSource
 */
interface EnvironmentPlugin {

  /**
   * @param TParseSource $source
   */
  public function parse(mixed $source): Document;

  public function denormalize(string $json): Document;

  public function normalize(Document $content): string;

  public function view(Document $content, ViewRequest $request): array;

}
