<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutContentHtmlParser;
use Drupal\niklan\ExternalContent\Nodes\ContainerDirective\ContainerDirectiveContentHtmlParser;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\RemoteVideoContentHtmlParser;
use Drupal\niklan\ExternalContent\Nodes\Video\VideoContentHtmlParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\niklan\ExternalContent\Extension\HtmlNodeParser>>
 */
final readonly class HtmlParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new RemoteVideoContentHtmlParser());
    $target->add(new VideoContentHtmlParser());
    $target->add(new CalloutContentHtmlParser());
    $target->add(new ContainerDirectiveContentHtmlParser(), -10);
  }

}
