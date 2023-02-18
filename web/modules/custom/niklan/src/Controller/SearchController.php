<?php declare(strict_types = 1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Data\ContentEntityResultSet;
use Drupal\niklan\Form\SearchForm;
use Drupal\niklan\Utility\SearchApiResultItemsHelper;
use Drupal\search_api\Utility\QueryHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides search page controller.
 */
final class SearchController implements ContainerInjectionInterface {

  /**
   * The amount of results per page.
   */
  protected const RESULT_LIMIT = 10;

  /**
   * Constructs a new SearchController instance.
   *
   * @param \Drupal\search_api\Utility\QueryHelperInterface $searchQueryHelper
   *   The Search API query helper.
   * @param \Drupal\Core\Form\FormBuilderInterface $formBuilder
   *   The form builder.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Pager\PagerManagerInterface $pagerManager
   *   The pager manager.
   */
  public function __construct(
    protected QueryHelperInterface $searchQueryHelper,
    protected FormBuilderInterface $formBuilder,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected PagerManagerInterface $pagerManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('search_api.query_helper'),
      $container->get('form_builder'),
      $container->get('entity_type.manager'),
      $container->get('pager.manager'),
    );
  }

  /**
   * Builds a search page.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return array
   *   An array with page content.
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

  /**
   * Builds a search page content.
   *
   * @param string|null $keys
   *   The search keys.
   *
   * @return array
   *   The page content.
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

    $search_results = $this->doSearch($keys);

    if ($search_results->getResultCount() === 0) {
      return $build;
    }

    $this->pagerManager->createPager(
      $search_results->getResultCount(),
      self::RESULT_LIMIT,
    );

    return $build + ['#results' => $search_results];
  }

  /**
   * Search for results.
   *
   * @param string $keys
   *   The search keys.
   *
   * @return \Drupal\niklan\Data\ContentEntityResultSet
   *   The search result IDs.
   */
  protected function doSearch(string $keys): ContentEntityResultSet {
    $current_page = $this->pagerManager->findPage();

    $index_storage = $this->entityTypeManager->getStorage('search_api_index');
    $index = $index_storage->load('global_index');

    $query = $this->searchQueryHelper->createQuery($index);
    $query->addCondition('search_api_datasource', 'entity:node');
    $query->keys($keys);
    $query->range($current_page * self::RESULT_LIMIT, self::RESULT_LIMIT);
    $query->sort('search_api_relevance', 'DESC');

    $result = $query->execute();
    \dump($result->getResultItems());

    return new ContentEntityResultSet(
      'node',
      SearchApiResultItemsHelper::extractEntityIds($result),
      (int) $result->getResultCount(),
    );
  }

  /**
   * Builds a page title.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function pageTitle(Request $request): TranslatableMarkup {
    $keys = $request->query->get('q');

    $title = new TranslatableMarkup('Search');

    if ($keys) {
      $title = new TranslatableMarkup(
        'Search results for «%keys»',
        ['%keys' => $keys],
      );
    }

    return $title;
  }

}
