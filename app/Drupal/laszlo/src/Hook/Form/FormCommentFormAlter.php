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
    $form['author']['mail']['#attributes']['placeholder'] = 'jackie@daytona.sport';
    $form['author']['mail']['#size'] = NULL;
    unset($form['author']['mail']['#description']);
    $form['author']['homepage']['#attributes']['placeholder'] = '';
    $form['author']['homepage']['#size'] = NULL;

    $form['#after_build'][] = [self::class, 'afterBuild'];
  }

}
