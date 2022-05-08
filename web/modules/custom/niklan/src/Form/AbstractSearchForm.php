<?php

declare(strict_types=1);

namespace Drupal\niklan\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides abstract search form.
 *
 * @codingStandardsIgnoreStart
 * @deprecated Replace by static HTML forms.
 * @codingStandardsIgnoreEnd
 */
abstract class AbstractSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['keys'] = [
      '#type' => 'textfield',
      '#placeholder' => new TranslatableMarkup('What are we looking for?'),
      '#default_value' => $form_state->get('default_keys'),
      '#theme_wrappers' => [],
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Search'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    if (\mb_strlen($form_state->getValue('keys')) > 0) {
      $url = Url::fromRoute('niklan.search_results', ['keys' => \urlencode($form_state->getValue('keys'))]);
    }
    else {
      $url = Url::fromRoute('niklan.search_page');
    }
    $response = new RedirectResponse($url->toString());
    $form_state->setResponse($response);
  }

}
