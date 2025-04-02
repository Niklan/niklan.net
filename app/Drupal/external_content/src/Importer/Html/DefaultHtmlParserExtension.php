<?php

declare(strict_types=1);

namespace Drupal\external_content\Importer\Html;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Importer\Html\Parser\CodeParser;
use Drupal\external_content\Importer\Html\Parser\FormatParser;
use Drupal\external_content\Importer\Html\Parser\HeadingParser;
use Drupal\external_content\Importer\Html\Parser\HtmlParser;
use Drupal\external_content\Importer\Html\Parser\ImageParser;
use Drupal\external_content\Importer\Html\Parser\LinkParser;
use Drupal\external_content\Importer\Html\Parser\ListItemParser;
use Drupal\external_content\Importer\Html\Parser\ListParser;
use Drupal\external_content\Importer\Html\Parser\ParagraphParser;
use Drupal\external_content\Importer\Html\Parser\TextParser;
use Drupal\external_content\Importer\Html\Parser\ThematicBreakParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Importer\Html\Parser\HtmlParser>
 */
final readonly class DefaultHtmlParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof HtmlParser);
    $target->addParser(new TextParser());
    $target->addParser(new ParagraphParser());
    $target->addParser(new FormatParser());
    $target->addParser(new HeadingParser());
    $target->addParser(new ImageParser());
    $target->addParser(new LinkParser());
    $target->addParser(new ListParser());
    $target->addParser(new ListItemParser());
    $target->addParser(new CodeParser());
    $target->addParser(new ThematicBreakParser());
  }

}
