<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms\PathProcessor;

use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

#[AutoconfigureTag('path_processor_outbound')]
final readonly class LlmsFormatPathProcessor implements OutboundPathProcessorInterface {

  public function __construct(
    private RequestStack $requestStack,
  ) {}

  #[\Override]
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    $currentRequest = $this->requestStack->getCurrentRequest();

    if ($currentRequest === NULL || $currentRequest->getRequestFormat() !== 'llms') {
      return $path;
    }

    $options['query']['_format'] = 'llms';

    return $path;
  }

}
