<?php

declare(strict_types=1);

namespace Drupal\external_content\Parser\Html;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Extension\ContainerExtensionManager;
use Drupal\external_content\Nodes\HtmlElement\HtmlParser as HtmlElementParser;
use Drupal\external_content\Nodes\Text\HtmlParser as TextParser;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Contract\Parser\Html\Parser>>
 */
#[Autoconfigure(
  tags: [
    ['name' => ContainerExtensionManager::TAG_NAME, 'id' => self::ID],
  ],
)]
final readonly class HtmlExtension implements Extension {

  public const string ID = 'external_content.html_parser';

  public function register(object $target): void {
    $target->add(new TextParser());
    // As a fallback for any other HTML element which is not parsed directly.
    $target->add(new HtmlElementParser(), -100);
  }

}
