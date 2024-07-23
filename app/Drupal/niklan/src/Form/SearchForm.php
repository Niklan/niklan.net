<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\niklan\Ajax\HistoryReplaceStateCommand;
use Drupal\niklan\Controller\SearchController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a search form for the search page.
 */
final class SearchForm extends FormBase {

  /**
   * Constructs a new SearchForm instance.
   *
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected ClassResolverInterface $classResolver,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('class_resolver'),
    );
  }

  #[\Override]
  public function getFormId(): string {
    return 'niklan_search';
  }

  #[\Override]
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['query'] = [
      '#type' => 'textfield',
      '#default_value' => $this->getRequest()->query->get('q'),
      '#attributes' => [
        'placeholder' => new TranslatableMarkup('Search'),
      ],
      '#theme_wrappers' => [],
      '#size' => 48,
    ];

    $form['search'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Search'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => [$this, 'onAjax'],
      ],
    ];

    $form['#cache']['contexts'][] = 'url.query_args:q';

    return $form;
  }

  #[\Override]
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $form_state->setRedirectUrl($this->buildResultsUrl($form_state));
  }

  /**
   * Process AJAX request for search.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function onAjax(array $form, FormStateInterface $form_state): AjaxResponse {
    $search_query = $form_state->getValue('query');
    $current_request = $this->getRequest();

    $current_request->query->remove('q');

    if ($search_query) {
      $current_request->query->set('q', $search_query);
    }

    // When search query is changed, page number should be reset.
    $current_request->query->remove('page');
    // Remove unnecessary Drupal's AJAX queries which will be presented if pager
    // will be rendered in AJAX request context.
    //
    // This is mainly done for pager, because otherwise it will add them for
    // links and break pagination.
    $current_request->query->remove(FormBuilderInterface::AJAX_FORM_REQUEST);
    $current_request->query->remove(MainContentViewSubscriber::WRAPPER_FORMAT);

    $this->requestStack->push($current_request);

    $search_controller = $this
      ->classResolver
      ->getInstanceFromDefinition(SearchController::class);

    $url = $this->buildResultsUrl($form_state)->toString();
    $page_title = $search_controller->pageTitle($current_request);
    $search_results = $search_controller->buildPageContent($search_query);

    $response = new AjaxResponse();
    $response->addCommand(new HistoryReplaceStateCommand($url));
    $response->addCommand(new HtmlCommand('h1', (string) $page_title));
    $response->addCommand(new ReplaceCommand(
      '.search-results',
      $search_results,
    ));

    return $response;
  }

  /**
   * Builds a results URL.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function buildResultsUrl(FormStateInterface $form_state): Url {
    $url_options = [];

    // Only add if value is provided to avoid empty ?q= in URL.
    if ($form_state->getValue('query')) {
      $url_options['query']['q'] = $form_state->getValue('query');
    }

    return Url::fromRoute(
      'niklan.search_page',
      options: $url_options,
    )->setAbsolute();
  }

}
