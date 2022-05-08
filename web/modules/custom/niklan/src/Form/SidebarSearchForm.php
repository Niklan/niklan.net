<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides sidebar search form for the page.
 */
final class SidebarSearchForm extends AbstractSearchForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'niklan_node_search_sidebar';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);
    $form['keys']['#attributes']['class'][] = 'niklan-node-search-sidebar__keys';
    $form['submit']['#value'] = '';
    $form['submit']['#attributes']['class'][] = 'niklan-node-search-sidebar__submit';
    return $form;
  }

}
