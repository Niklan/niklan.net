<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\Callout\HtmlParser as CalloutParser;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\HtmlParser as CodeBlockParser;
use Drupal\niklan\ExternalContent\Nodes\LocalVideo\HtmlParser as LocalVideoParser;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\HtmlParser as RemoteVideoParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\niklan\ExternalContent\Extension\HtmlNodeParser>>
 */
final readonly class HtmlParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new RemoteVideoParser());
    $target->add(new LocalVideoParser());
    $target->add(new CalloutParser());
    $target->add(new CodeBlockParser());
  }

}
