<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Form;

use Drupal\Core\Form\FormStateInterface;

final readonly class FormCommentFormAlter {

  public static function afterBuild(array $form, FormStateInterface $form_state): array {
    $form['comment_body']['widget'][0]['format']['#access'] = FALSE;

    return $form;
  }

  public function __invoke(array &$form, FormStateInterface $form_state, string $form_id): void {
    $form['author']['name']['#attributes']['placeholder'] = 'Laszlo Cravensworth';
    $form['author']['name']['#size'] = NULL;
    $form['author']['name']['#start_decorator'] = [
      '#type' => 'component',
      '#component' => 'laszlo:icon',
      '#props' => [
        'icon' => 'user',
      ],
    ];

    $form['author']['mail']['#attributes']['placeholder'] = 'jackie@daytona.sport';
    $form['author']['mail']['#size'] = NULL;
    $form['author']['mail']['#start_decorator'] = [
      '#type' => 'component',
      '#component' => 'laszlo:icon',
      '#props' => [
        'icon' => 'dialog',
      ],
    ];

    unset($form['author']['mail']['#description']);
    $form['author']['homepage']['#attributes']['placeholder'] = '';
    $form['author']['homepage']['#size'] = NULL;
    $form['author']['homepage']['#start_decorator'] = [
      '#type' => 'component',
      '#component' => 'laszlo:icon',
      '#props' => [
        'icon' => 'link',
      ],
    ];

    if (isset($form['actions']['preview'])) {
      $form['actions']['preview']['#button_type'] = 'secondary';
      $form['actions']['preview']['#button_variant'] = 'outlined';
    }

    $form['#after_build'][] = [self::class, 'afterBuild'];
  }

}
