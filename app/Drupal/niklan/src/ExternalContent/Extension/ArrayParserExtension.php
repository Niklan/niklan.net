<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\Callout\ArrayParser as CalloutParser;
use Drupal\niklan\ExternalContent\Nodes\CalloutBody\ArrayParser as CalloutBodyParser;
use Drupal\niklan\ExternalContent\Nodes\CalloutTitle\ArrayParser as CalloutTitleParser;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\ArrayParser as CodeBlockParser;
use Drupal\niklan\ExternalContent\Nodes\MediaReference\ArrayParser as MediaReferenceParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\niklan\ExternalContent\Extension\ArrayElementParser>>
 */
final readonly class ArrayParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new CodeBlockParser());
    $target->add(new CalloutParser());
    $target->add(new CalloutTitleParser());
    $target->add(new CalloutBodyParser());
    $target->add(new MediaReferenceParser());
  }

}
