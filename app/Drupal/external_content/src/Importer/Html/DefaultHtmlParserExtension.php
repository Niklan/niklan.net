<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Importer\Html\Parser\CodeParser;
use Drupal\external_content\Importer\Html\Parser\FormatParser;
use Drupal\external_content\Importer\Html\Parser\HeadingParser;
use Drupal\external_content\Importer\Html\Parser\ImageParser;
use Drupal\external_content\Importer\Html\Parser\LinkParser;
use Drupal\external_content\Importer\Html\Parser\ListItemParser;
use Drupal\external_content\Importer\Html\Parser\ListParser;
use Drupal\external_content\Importer\Html\Parser\ParagraphParser;
use Drupal\external_content\Importer\Html\Parser\TextParser;
use Drupal\external_content\Importer\Html\Parser\ThematicBreakParser;
use Drupal\external_content\Utils\Registry;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\external_content\Importer\Html\HtmlNodeParser>>
 */
final readonly class DefaultHtmlParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new TextParser());
    $target->add(new ParagraphParser());
    $target->add(new FormatParser());
    $target->add(new HeadingParser());
    $target->add(new ImageParser());
    $target->add(new LinkParser());
    $target->add(new ListParser());
    $target->add(new ListItemParser());
    $target->add(new CodeParser());
    $target->add(new ThematicBreakParser());
  }

}
