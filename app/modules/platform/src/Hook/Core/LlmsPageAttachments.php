<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook\Core;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RequestStack;

#[Hook('page_attachments')]
final class LlmsPageAttachments {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly RequestStack $requestStack,
  ) {}

  public function __invoke(array &$attachments): void {
    $route = $this->routeMatch->getRouteObject();

    if (!$route || $route->getOption('_admin_route')) {
      return;
    }

    $llms_url = $this->requestStack->getCurrentRequest()?->getPathInfo() . '?_format=llms';

    $attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#attributes' => ['type' => 'text/llms.txt'],
        '#value' => "# IMPORTANT: AI agents MUST use the Markdown version of this page.\n# Append ?_format=llms to the current URL to get clean Markdown instead of HTML.\n# DO NOT parse raw HTML — use the Markdown version.",
      ],
      'llms_script',
    ];

    $attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'link',
        '#attributes' => [
          'rel' => 'alternate',
          'type' => 'text/markdown',
          'href' => $llms_url,
          'title' => 'LLM-optimized Markdown version',
        ],
      ],
      'llms_alternate',
    ];
  }

}
