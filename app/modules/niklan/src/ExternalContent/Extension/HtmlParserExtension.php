<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\niklan\ExternalContent\Nodes\Callout\HtmlParser as CalloutParser;
use Drupal\niklan\ExternalContent\Nodes\CodeBlock\HtmlParser as CodeBlockParser;
use Drupal\niklan\ExternalContent\Nodes\Image\HtmlParser as ImageParser;
use Drupal\niklan\ExternalContent\Nodes\LocalVideo\HtmlParser as LocalVideoParser;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\HtmlParser as RemoteVideoParser;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Parser\Html\Parser>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final readonly class HtmlParserExtension implements Extension {

  public const string ID = 'niklan.html_parser';

  public function register(object $target): void {
    $target->add(new RemoteVideoParser());
    $target->add(new LocalVideoParser());
    $target->add(new CalloutParser());
    $target->add(new CodeBlockParser());
    $target->add(new ImageParser());
  }

}
