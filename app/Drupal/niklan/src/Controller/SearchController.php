<?php declare(strict_types = 1);

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

/**
 * Provides search page controller.
 */
final class SearchController implements SearchControllerInterface, ContainerInjectionInterface {

  /**
   * The amount of results per page.
   */
  protected const RESULT_LIMIT = 10;

  /**
   * Constructs a new SearchController instance.
   *
   * @param \Drupal\niklan\Search\EntitySearchInterface $entitySearch
   *   The entity search.
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The form builder.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Pager\PagerManagerInterface $pagerManager
   *   The pager manager.
   */
  public function __construct(
    protected EntitySearchInterface $entitySearch,
    protected FormBuilderInterface $formBuilder,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected PagerManagerInterface $pagerManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('niklan.search.global'),
      $container->get('form_builder'),
      $container->get('entity_type.manager'),
      $container->get('pager.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function page(Request $request): array {
    $keys = $request->query->get('q');

    return [
      '#theme' => 'niklan_search_page',
      '#header' => $this->buildPageHeader(),
      '#content' => $this->buildPageContent($keys),
    ];
  }

  /**
   * {@inheritdoc}
   */
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

  /**
   * {@inheritdoc}
   */
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

  /**
   * Builds a search page header.
   *
   * @return array
   *   The header content.
   */
  protected function buildPageHeader(): array {
    return [
      'search_form' => $this->formBuilder->getForm(SearchForm::class),
    ];
  }

}
