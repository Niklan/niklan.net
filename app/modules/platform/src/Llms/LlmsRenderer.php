<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Render\MainContent\MainContentRendererInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class LlmsRenderer implements MainContentRendererInterface {

  public function __construct(
    private TitleResolverInterface $titleResolver,
    private RendererInterface $renderer,
    private HtmlToMarkdownConverter $htmlToMarkdownConverter,
    private EventDispatcherInterface $eventDispatcher,
  ) {}

  #[\Override]
  public function renderResponse(array $main_content, Request $request, RouteMatchInterface $route_match): CacheableResponse {
    $renderEvent = new LlmsRenderEvent($main_content, $request, $route_match);
    $this->eventDispatcher->dispatch($renderEvent);

    $cacheableMetadata = CacheableMetadata::createFromRenderArray($main_content);

    $cacheableMetadata = $cacheableMetadata->merge($renderEvent->getCacheableMetadata());

    if ($renderEvent->hasCustomMarkdown()) {
      $markdown = $renderEvent->getMarkdown() ?? '';
    }
    else {
      $renderContext = new RenderContext();
      $rendered = $this->renderer->executeInRenderContext($renderContext, function () use (&$main_content): string {
        return (string) $this->renderer->render($main_content);
      });
      \assert(\is_string($rendered));
      $markup = $rendered;

      if (!$renderContext->isEmpty()) {
        $bubbleable = $renderContext->pop();
        \assert($bubbleable instanceof CacheableMetadata);
        $cacheableMetadata = $cacheableMetadata->merge($bubbleable);
      }

      $markdown = $this->htmlToMarkdownConverter->convert($markup);
    }

    $title = $renderEvent->getTitle() ?? $this->resolveTitle($main_content, $request, $route_match);

    if ($title !== '') {
      $markdown = "# {$title}\n\n{$markdown}";
    }

    $alterEvent = new LlmsResponseAlterEvent($markdown, $request, $route_match);
    $this->eventDispatcher->dispatch($alterEvent);

    $response = new CacheableResponse($alterEvent->getMarkdown(), 200, [
      'Content-Type' => 'text/markdown; charset=UTF-8',
      'X-Robots-Tag' => 'noindex',
    ]);
    $response->addCacheableDependency($cacheableMetadata);

    return $response;
  }

  private function resolveTitle(array $main_content, Request $request, RouteMatchInterface $route_match): string {
    $route = $route_match->getRouteObject();

    if ($route === NULL) {
      return '';
    }

    $title = $this->titleResolver->getTitle($request, $route);

    if ($title === NULL || \is_array($title)) {
      return '';
    }

    return (string) $title;
  }

}
