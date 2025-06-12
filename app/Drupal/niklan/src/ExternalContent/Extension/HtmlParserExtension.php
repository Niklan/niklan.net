<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutHtmlParser;
use Drupal\niklan\ExternalContent\Nodes\ContainerDirective\ContainerDirectiveHtmlParser;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\RemoteVideoHtmlParser;
use Drupal\niklan\ExternalContent\Nodes\Video\VideoHtmlParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\niklan\ExternalContent\Extension\HtmlNodeParser>>
 */
final readonly class HtmlParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new RemoteVideoHtmlParser());
    $target->add(new VideoHtmlParser());
    $target->add(new CalloutHtmlParser());
    $target->add(new ContainerDirectiveHtmlParser(), -10);
  }

}
