<?php

declare(strict_types=1);

namespace Drupal\niklan\Search\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Search\Data\SearchParams;
use Drupal\niklan\Search\Form\SearchForm;
use Drupal\niklan\Search\Repository\EntitySearch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo Refactor.
 */
final class Search implements ContainerInjectionInterface {

  protected const RESULT_LIMIT = 10;

  public function __construct(
    protected EntitySearch $entitySearch,
    protected FormBuilderInterface $formBuilder,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected PagerManagerInterface $pagerManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('niklan.search.global'),
      $container->get('form_builder'),
      $container->get('entity_type.manager'),
      $container->get('pager.manager'),
    );
  }

  public function buildPageContent(?string $keys): array {
    $build = [
      '#theme' => 'niklan_search_results',
      '#cache' => [
        'tags' => [
          'search_api_list:global_index',
        ],
      ],
    ];

    if (!$keys) {
      return $build;
    }

    $search_params = new SearchParams(
      $keys,
      self::RESULT_LIMIT,
      $this->pagerManager->findPage() * self::RESULT_LIMIT,
    );
    $search_results = $this->entitySearch->search($search_params);

    if ($search_results->getTotalResultsCount() === 0) {
      return $build;
    }

    $this->pagerManager->createPager(
      $search_results->getTotalResultsCount(),
      self::RESULT_LIMIT,
    );

    return $build + ['#results' => $search_results];
  }

  public function pageTitle(Request $request): string {
    $keys = $request->query->get('q');

    $title = new TranslatableMarkup('Search');

    if ($keys) {
      $title = new TranslatableMarkup(
        'Search results for «%keys»',
        ['%keys' => $keys],
      );
    }

    return (string) $title;
  }

  protected function buildPageHeader(): array {
    return [
      'search_form' => $this->formBuilder->getForm(SearchForm::class),
    ];
  }

  public function __invoke(Request $request): array {
    $keys = $request->query->get('q');

    return [
      '#theme' => 'niklan_search_page',
      '#header' => $this->buildPageHeader(),
      '#content' => $this->buildPageContent($keys),
    ];
  }

}
