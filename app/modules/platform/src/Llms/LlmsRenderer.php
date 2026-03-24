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
    $render_event = new LlmsRenderEvent($main_content, $request, $route_match);
    $this->eventDispatcher->dispatch($render_event);

    $cacheable_metadata = CacheableMetadata::createFromRenderArray($main_content);

    $cacheable_metadata = $cacheable_metadata->merge($render_event->getCacheableMetadata());

    if ($render_event->hasCustomMarkdown()) {
      $markdown = $render_event->getMarkdown() ?? '';
    }
    else {
      $render_context = new RenderContext();
      $rendered = $this->renderer->executeInRenderContext($render_context, function () use (&$main_content): string {
        return (string) $this->renderer->render($main_content);
      });
      \assert(\is_string($rendered));
      $markup = $rendered;

      if (!$render_context->isEmpty()) {
        $bubbleable = $render_context->pop();
        \assert($bubbleable instanceof CacheableMetadata);
        $cacheable_metadata = $cacheable_metadata->merge($bubbleable);
      }

      $markdown = $this->htmlToMarkdownConverter->convert($markup);
    }

    $title = $render_event->getTitle() ?? $this->resolveTitle($main_content, $request, $route_match);

    if ($title !== '') {
      $markdown = "# {$title}\n\n{$markdown}";
    }

    $alter_event = new LlmsResponseAlterEvent($markdown, $request, $route_match);
    $this->eventDispatcher->dispatch($alter_event);

    $cacheable_metadata = $cacheable_metadata->merge($alter_event->getCacheableMetadata());

    $response = new CacheableResponse($alter_event->getMarkdown(), 200, [
      'Content-Type' => 'text/markdown; charset=UTF-8',
      'X-Robots-Tag' => 'noindex',
    ]);
    $response->addCacheableDependency($cacheable_metadata);

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
