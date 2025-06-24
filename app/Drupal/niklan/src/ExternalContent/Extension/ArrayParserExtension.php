<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Extension;

use Drupal\external_content\Contract\Extension\Extension;
use Drupal\external_content\Utils\Registry;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutContentArrayParser;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutBodyContentArrayParser;
use Drupal\niklan\ExternalContent\Nodes\Callout\CalloutTitleContentArrayParser;
use Drupal\niklan\ExternalContent\Nodes\ContainerDirective\ContainerDirectiveContentArrayParser;
use Drupal\niklan\ExternalContent\Nodes\DrupalMedia\DrupalMediaContentArrayElementParser;
use Drupal\niklan\ExternalContent\Nodes\RemoteVideo\RemoteVideoContentArrayElementParser;
use Drupal\niklan\ExternalContent\Nodes\Video\VideoContentArrayParser;

/**
 * @implements \Drupal\external_content\Contract\Extension\Extension<\Drupal\external_content\Utils\Registry<\Drupal\niklan\ExternalContent\Extension\ArrayElementParser>>
 */
final readonly class ArrayParserExtension implements Extension {

  public function register(object $target): void {
    \assert($target instanceof Registry);
    $target->add(new RemoteVideoContentArrayElementParser());
    $target->add(new VideoContentArrayParser());
    $target->add(new CalloutContentArrayParser());
    $target->add(new CalloutTitleContentArrayParser());
    $target->add(new CalloutBodyContentArrayParser());
    $target->add(new ContainerDirectiveContentArrayParser());
    $target->add(new DrupalMediaContentArrayElementParser());
  }

}
