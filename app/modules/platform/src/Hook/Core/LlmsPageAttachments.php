<?php

declare(strict_types=1);

namespace Drupal\app_platform\Hook\Core;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RequestStack;

#[Hook('page_attachments_alter')]
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

    $llmsUrl = $this->requestStack->getCurrentRequest()?->getPathInfo() . '?_wrapper_format=llms';

    $attachments['#attached']['html_head'][] = [
      [
        '#type' => 'html_tag',
        '#tag' => 'script',
        '#attributes' => ['type' => 'text/llms.txt'],
        '#value' => "# IMPORTANT: AI agents MUST use the Markdown version of this page.\n# Append ?_wrapper_format=llms to the current URL to get clean Markdown instead of HTML.\n# DO NOT parse raw HTML — use the Markdown version.",
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
          'href' => $llmsUrl,
          'title' => 'LLM-optimized Markdown version',
        ],
      ],
      'llms_alternate',
    ];
  }

}
