<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

final readonly class FormCommentFormAlter {

  public static function afterBuild(array $form, FormStateInterface $form_state): array {
    $form['comment_body']['widget'][0]['format']['#access'] = FALSE;

    return $form;
  }

  public function __invoke(array &$form, FormStateInterface $form_state, string $form_id): void {
    $form['author']['name']['#placeholder'] = new TranslatableMarkup('Laszlo Cravensworth');
    $form['author']['name']['#size'] = NULL;
    $form['author']['name']['#component_data']['start_decorator'] = [
      '#type' => 'component',
      '#component' => 'laszlo:icon',
      '#props' => [
        'icon' => 'user',
      ],
    ];

    $form['author']['mail']['#placeholder'] = 'jackie@daytona.sport';
    $form['author']['mail']['#size'] = NULL;
    $form['author']['mail']['#component_data']['start_decorator'] = [
      '#type' => 'component',
      '#component' => 'laszlo:icon',
      '#props' => [
        'icon' => 'mail',
      ],
    ];

    unset($form['author']['mail']['#description']);
    $form['author']['homepage']['#placeholder'] = 'https://skcus.olzsal';
    $form['author']['homepage']['#size'] = NULL;
    $form['author']['homepage']['#component_data']['start_decorator'] = [
      '#type' => 'component',
      '#component' => 'laszlo:icon',
      '#props' => [
        'icon' => 'link',
      ],
    ];

    if (isset($form['actions']['preview'])) {
      $form['actions']['preview']['#component_data']['color'] = 'secondary';
    }

    $form['#after_build'][] = [self::class, 'afterBuild'];
  }

}
