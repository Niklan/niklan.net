<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Data\SearchParams;
use Drupal\niklan\Form\SearchForm;
use Drupal\niklan\Search\EntitySearchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

final class SearchController implements SearchControllerInterface, ContainerInjectionInterface {

  protected const RESULT_LIMIT = 10;

  public function __construct(
    protected EntitySearchInterface $entitySearch,
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

  #[\Override]
  public function page(Request $request): array {
    $keys = $request->query->get('q');

    return [
      '#theme' => 'niklan_search_page',
      '#header' => $this->buildPageHeader(),
      '#content' => $this->buildPageContent($keys),
    ];
  }

  #[\Override]
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

  #[\Override]
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

}
