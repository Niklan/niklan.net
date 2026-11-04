<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

#[AsDecorator(decorates: 'title_resolver')]
final readonly class PageSuffixTitleResolver implements TitleResolverInterface {

  public function __construct(
    #[AutowireDecorated]
    private TitleResolverInterface $inner,
    private PagerManagerInterface $pagerManager,
    private RendererInterface $renderer,
    private TranslationInterface $translation,
    private RequestStack $requestStack,
  ) {}

  /**
   * @return string|array<string, mixed>|\Stringable|null
   */
  public function getTitle(Request $request, Route $route): string|array|\Stringable|NULL {
    $title = $this->inner->getTitle($request, $route);

    if (!$route->getDefault('_title_page_suffix') || !$title) {
      return $title;
    }

    if ($request !== $this->requestStack->getCurrentRequest()) {
      return $title;
    }

    $page_number = $this->pagerManager->findPage();
    if ($page_number < 1) {
      return $title;
    }

    $title_string = $this->toStringable($title);
    if ($title_string === NULL) {
      return $title;
    }

    return $this->translation->translate(
      string: '@title — page #@number',
      args: ['@title' => $title_string, '@number' => $page_number + 1],
    );
  }

  /**
   * Renders render arrays; non-stringable titles are not supported.
   */
  private function toStringable(mixed $title): string|\Stringable|NULL {
    if (Element::isRenderArray($title)) {
      \assert(\is_array($title));
      $title = $this->renderer->renderInIsolation($title);
    }

    return \is_string($title) || $title instanceof \Stringable ? $title : NULL;
  }

}
