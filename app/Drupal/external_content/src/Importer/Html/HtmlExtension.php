<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Nodes\Format\HtmlParser as FormatParser;
use Drupal\external_content\Nodes\Heading\HtmlParser as HeadingParser;
use Drupal\external_content\Nodes\HtmlElement\HtmlParser as HtmlElementParser;
use Drupal\external_content\Nodes\Text\HtmlParser as TextParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Html\HtmlNodeParser>>
 */
final readonly class HtmlExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new TextParser());
    $target->add(new FormatParser());
    $target->add(new HeadingParser());
    // As a fallback for any other HTML element which is not parsed directly.
    $target->add(new HtmlElementParser(), -100);
  }

}
