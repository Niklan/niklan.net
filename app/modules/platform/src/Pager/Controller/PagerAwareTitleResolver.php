<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager\Controller;

use Drupal\app_platform\Http\ErrorPageDetector;
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
    private TranslationInterface $stringTranslation,
    private RequestStack $requestStack,
    private ErrorPageDetector $errorPageDetector,
  ) {}

  #[\Override]
  public function getTitle(Request $request, Route $route): string|array|\Stringable|NULL {
    $title = $this->inner->getTitle($request, $route);

    if (!$title || !$this->shouldAddSuffix($request)) {
      return $title;
    }

    if (Element::isRenderArray($title)) {
      \assert(\is_array($title));
      $title = $this->renderer->renderInIsolation($title);
    }

    if (!$this->isStringable($title)) {
      return $title;
    }

    return (string) $title . ' — ' . (string) $this->stringTranslation->translate('page #@number', [
      '@number' => ($this->pagerManager->getPager()?->getCurrentPage() ?? 0) + 1,
    ]);
  }

  /**
   * The pager manager is a process-wide singleton, not scoped to $request.
   *
   * Breadcrumb builders resolve titles for ancestor routes using synthetic
   * sub-requests, in which case the pager belongs to an unrelated pager on
   * the actual current page (e.g. node comments), not to this route.
   */
  private function shouldAddSuffix(Request $request): bool {
    if (!$request->attributes->has('_title_pager_suffix') || $this->errorPageDetector->isErrorPageSubrequest() || !$this->isCurrentRequest($request)) {
      return FALSE;
    }

    return !$this->isFirstPage();
  }

  private function isCurrentRequest(Request $request): bool {
    $current_request = $this->requestStack->getCurrentRequest();

    return $current_request !== NULL && $request->attributes->get('_route') === $current_request->attributes->get('_route');
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
