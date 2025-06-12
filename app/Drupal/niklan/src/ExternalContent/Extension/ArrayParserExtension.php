<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutArrayParser;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutBodyArrayParser;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutTitleArrayParser;
use Drupal\niklan\ExternalContent\Nodes\ContainerDirective\ContainerDirectiveArrayParser;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\RemoteVideoArrayElementParser;
use Drupal\niklan\ExternalContent\Nodes\Video\VideoArrayParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\niklan\ExternalContent\Extension\ArrayElementParser>>
 */
final readonly class ArrayParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new RemoteVideoArrayElementParser());
    $target->add(new VideoArrayParser());
    $target->add(new CalloutArrayParser());
    $target->add(new CalloutTitleArrayParser());
    $target->add(new CalloutBodyArrayParser());
    $target->add(new ContainerDirectiveArrayParser());
  }

}
