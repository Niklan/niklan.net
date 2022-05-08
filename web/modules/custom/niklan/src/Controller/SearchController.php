<?php

declare(strict_types=1);

namespace Drupal\niklan\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Pager\PagerParametersInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\niklan\Form\SearchForm;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\search_api\Utility\QueryHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides search page controller.
 *
 * @todo Replace render array markup with theme hook.
 */
final class SearchController implements ContainerInjectionInterface {

  /**
   * The query helper.
   */
  protected QueryHelperInterface $queryHelper;

  /**
   * The form builder.
   */
  protected FormBuilderInterface $formBuilder;

  /**
   * The index storage.
   */
  protected EntityStorageInterface $indexStorage;

  /**
   * The amount of results per page.
   */
  protected int $limit = 10;

  /**
   * The pager manager.
   */
  protected PagerManagerInterface $pagerManager;

  /**
   * The pager parameters.
   */
  protected PagerParametersInterface $pagerParameters;

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new self();
    $instance->queryHelper = $container->get('search_api.query_helper');
    $instance->formBuilder = $container->get('form_builder');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->indexStorage = $instance->entityTypeManager->getStorage('search_api_index');
    $instance->pagerManager = $container->get('pager.manager');
    $instance->pagerParameters = $container->get('pager.parameters');

    return $instance;
  }

  /**
   * Builds a search page.
   *
   * @param string|null $keys
   *   The search keys.
   *
   * @return array
   *   An array with page content.
   */
  public function build(?string $keys = NULL): array {
    $keys = $keys ? \urldecode($keys) : $keys;
    $title = new TranslatableMarkup('Search');
    if ($keys) {
      $title = new TranslatableMarkup('Search results for «%keys»', ['%keys' => $keys]);
    }

    return [
      '#title' => $title,
      '#theme' => 'niklan_search_page',
      '#search_form' => $this->buildSearchForm($keys),
      '#results' => $this->buildResults($keys),
      '#pager' => $this->buildPager(),
    ];
  }

  /**
   * Builds a search form.
   *
   * @param string|null $keys
   *   The search keys.
   *
   * @return array
   *   An array with search form.
   */
  protected function buildSearchForm(?string $keys = NULL): array {
    $form_state = new FormState();
    if ($keys) {
      $form_state->set('default_keys', $keys);
    }

    return [
      '#theme_wrappers' => [
        'container' => [
          '#attributes' => [
            'class' => ['search-page__form'],
          ],
        ],
      ],
      'form' => $this->formBuilder->buildForm(SearchForm::class, $form_state),
    ];
  }

  /**
   * Builds results.
   *
   * @param string|null $keys
   *   The search keys.
   *
   * @return array
   *   An array with prepared results.
   */
  protected function buildResults(?string $keys = NULL): array {
    if (!$keys) {
      return [];
    }
    else {
      return $this->buildSearchResults($keys);
    }
  }

  /**
   * Builds a search results.
   *
   * @param string $keys
   *   The search keys.
   *
   * @return array
   *   An array with results.
   */
  protected function buildSearchResults(string $keys): array {
    $result_set = $this->doSearch($keys);
    if ($result_set->getResultCount() == 0) {
      return [];
    }
    else {
      return $this->buildResultItems($result_set);
    }
  }

  /**
   * Search for results.
   *
   * @param string $keys
   *   The keywords.
   *
   * @throws \Drupal\search_api\SearchApiException
   */
  protected function doSearch(string $keys): ResultSetInterface {
    $offset = $this->pagerParameters->findPage() * $this->limit;

    $index = $this->indexStorage->load('global_index');
    $query = $this->queryHelper->createQuery($index);
    $query->keys($keys);
    $query->range($offset, $this->limit);
    $query->sort('search_api_relevance', 'DESC');
    $result_set = $query->execute();

    $this->pagerManager->createPager($result_set->getResultCount(), $this->limit);

    return $result_set;
  }

  /**
   * Builds result items.
   *
   * @return array
   *   An array with built results.
   */
  protected function buildResultItems(ResultSetInterface $result_set): array {
    $items = [];
    foreach ($result_set->getResultItems() as $item) {
      $entity = $item->getOriginalObject()->getValue();
      $view_builder = $this->entityTypeManager->getViewBuilder($entity->getEntityTypeId());
      $items[] = $view_builder->view($entity, 'search_result');
    }
    return $items;
  }

  /**
   * Builds pager element.
   *
   * @return array
   *   The render array with pager.
   */
  protected function buildPager(): array {
    return [
      '#type' => 'pager',
      '#quantity' => 4,
    ];
  }

  /**
   * Builds no results.
   *
   * @return array
   *   An arary with element.
   */
  protected function buildNoResults(): array {
    return [
      '#theme_wrappers' => [
        'container' => [
          '#attributes' => [
            'class' => ['search-page__no-results'],
          ],
        ],
      ],
      'value' => [
        '#markup' => new TranslatableMarkup('No results found.'),
      ],
    ];
  }

}
