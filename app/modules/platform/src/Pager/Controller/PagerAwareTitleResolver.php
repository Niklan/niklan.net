<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager\Controller;

use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * @ingroup seo_pager
 */
#[AsDecorator(decorates: 'title_resolver', priority: -100)]
final readonly class PagerAwareTitleResolver implements TitleResolverInterface {

  public function __construct(
    #[AutowireDecorated]
    private TitleResolverInterface $inner,
    private PagerManagerInterface $pagerManager,
    private RendererInterface $renderer,
  ) {}

  #[\Override]
  public function getTitle(Request $request, Route $route): string|array|\Stringable|null {
    $title = $this->inner->getTitle($request, $route);

    if (!$request->attributes->has('_title_pager_suffix') || !$title) {
      return $title;
    }

    if ($this->isFirstPage()) {
      return $title;
    }

    if (Element::isRenderArray($title)) {
      \assert(\is_array($title));
      $title = $this->renderer->renderInIsolation($title);
    }

    if (!$this->isStringable($title)) {
      return $title;
    }

    return (string) $title . ' — ' . (string) new TranslatableMarkup('page #@number', [
      '@number' => ($this->pagerManager->getPager()?->getCurrentPage() ?? 0) + 1,
    ]);
  }

  private function isFirstPage(): bool {
    $pager = $this->pagerManager->getPager();
    if ($pager === NULL) {
      return TRUE;
    }

    return $pager->getTotalPages() < 1 || $pager->getCurrentPage() < 1;
  }

  /**
   * @phpstan-assert-if-true string|\Stringable $title
   */
  private function isStringable(mixed $title): bool {
    return \is_string($title) || $title instanceof \Stringable;
  }

}
