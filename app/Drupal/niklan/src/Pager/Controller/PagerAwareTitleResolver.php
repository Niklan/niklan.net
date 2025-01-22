<?php

declare(strict_types=1);

namespace Drupal\niklan\Pager\Controller;

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

    if (!$route->hasDefault('_title_pager_suffix') || !$title) {
      return $title;
    }

    // Do not add suffix on the first page.
    if ($this->pagerManager->getPager()?->getTotalPages() < 1 || $this->pagerManager->getPager()->getCurrentPage() < 1) {
      return $title;
    }

    if (Element::isRenderArray($title)) {
      $title = $this->renderer->renderInIsolation($title);
    }

    if (!\is_string($title) && !$title instanceof \Stringable) {
      return $title;
    }

    return $title . ' — ' . new TranslatableMarkup('page #@number', [
      '@number' => $this->pagerManager->getPager()->getCurrentPage() + 1,
    ]);
  }

}
