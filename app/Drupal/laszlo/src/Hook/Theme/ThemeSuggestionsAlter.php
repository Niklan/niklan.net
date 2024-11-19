<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\comment\Entity\Comment;
use Drupal\Core\Entity\EntityInterface;
use Drupal\taxonomy\TermInterface;

final readonly class ThemeSuggestionsAlter {

  public function __invoke(array &$suggestions, array &$variables, string $hook): void {
    match ($hook) {
      default => NULL,
      'taxonomy_term' => $this->alterTaxonomyTerm($suggestions, $variables),
      'comment' => $this->alterComment($suggestions, $variables),
    };
  }

  private function alterComment(array &$suggestions, array &$variables): void {
    $comment = $variables['elements']['#comment'];
    \assert($comment instanceof Comment);
    $view_mode = $variables['elements']['#view_mode'];

    $this->addGenericEntitySuggestions($suggestions, $view_mode, $comment);
  }

  private function alterTaxonomyTerm(array &$suggestions, array &$variables): void {
    $taxonomy_term = $variables['elements']['#taxonomy_term'];
    \assert($taxonomy_term instanceof TermInterface);
    $view_mode = $variables['elements']['#view_mode'];

    $this->addGenericEntitySuggestions($suggestions, $view_mode, $taxonomy_term);
  }

  private function addGenericEntitySuggestions(array &$suggestions, string $view_mode, EntityInterface $entity): void {
    $entity_type_id = $entity->getEntityTypeId();
    $entity_id = $entity->id();
    $bundle = $entity->bundle();

    // Reset suggestions to keep a proper priority and remove useless
    // suggestions.
    $suggestions = [];
    // It is intentionally prefixed with a subtype. Without it, there would be a
    // naming collision. For example, "bundle" and "view_mode" could have the
    // same name, and that would cause serious problems.
    $suggestions[] = "{$entity_type_id}__view_mode__$view_mode";
    $suggestions[] = "{$entity_type_id}__bundle__$bundle";
    $suggestions[] = "{$entity_type_id}__id__$entity_id";
    $suggestions[] = "{$entity_type_id}__{$bundle}__$view_mode";
    $suggestions[] = "{$entity_type_id}__{$entity_id}__$view_mode";
  }

}
