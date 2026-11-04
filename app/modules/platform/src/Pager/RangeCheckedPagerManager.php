<?php

declare(strict_types=1);

namespace Drupal\app_platform\Pager;

use Drupal\Core\Pager\Pager;
use Drupal\Core\Pager\PagerManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsDecorator(decorates: 'pager.manager')]
final readonly class RangeCheckedPagerManager implements PagerManagerInterface {

  public function __construct(
    #[AutowireDecorated]
    private PagerManagerInterface $inner,
    private RequestStack $requestStack,
  ) {}

  public function createPager($total, $limit, $element = 0): Pager {
    $pager = $this->inner->createPager($total, $limit, $element);

    // The error page is a subrequest that inherits the query, so only the main
    // request is checked, otherwise the 404 page would throw 404 itself.
    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL || $request !== $this->requestStack->getMainRequest() || PaginationPathPolicy::isExcluded($request->getPathInfo())) {
      return $pager;
    }

    // The pager clamps the current page, so compare the requested one.
    $page = $this->inner->findPage($element);
    if ($page > 0 && $page >= $pager->getTotalPages()) {
      throw new NotFoundHttpException();
    }

    return $pager;
  }

  public function getPager($element = 0): ?Pager {
    return $this->inner->getPager($element);
  }

  public function findPage(int $pager_id = 0): int {
    return $this->inner->findPage($pager_id);
  }

  public function getUpdatedParameters(array $query, $element, $index): array {
    return $this->inner->getUpdatedParameters($query, $element, $index);
  }

  public function getMaxPagerElementId(): int {
    return $this->inner->getMaxPagerElementId();
  }

  public function reservePagerElementId(int $element): void {
    $this->inner->reservePagerElementId($element);
  }

}
