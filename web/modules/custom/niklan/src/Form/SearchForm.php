<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides default search form for the page.
 */
final class SearchForm extends AbstractSearchForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'niklan_node_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);
    $form['keys']['#attributes']['class'][] = 'niklan-node-search__submit';
    $form['submit']['#attributes']['class'][] = 'button--primary';

    return $form;
  }

}
