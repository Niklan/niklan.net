<?php declare(strict_types = 1);

namespace Drupal\Tests\niklan\Traits;

use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\TermInterface;

/**
 * Provides a trait with helpers for 'tags' vocabulary.
 */
trait TagsTrait {

  /**
   * Sets up tags vocabulary.
   */
  protected function setUpTagsVocabulary(): void {
    $this->installEntitySchema('taxonomy_term');
    $this->installEntitySchema('taxonomy_vocabulary');

    Vocabulary::create(['vid' => 'tags']);
  }

  /**
   * Creates a tag.
   *
   * @param array $values
   *   The tag values.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   The tag entity.
   */
  protected function createTag(array $values = []): TermInterface {
    $default_values = [
      'vid' => 'tags',
      'name' => $this->randomString(),
      'status' => NodeInterface::PUBLISHED,
    ];

    return Term::create($values + $default_values);
  }

}
