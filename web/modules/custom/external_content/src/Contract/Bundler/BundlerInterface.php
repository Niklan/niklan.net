<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Contract\Source\SourceInterface;

/**
 * Represents an external content bundler.
 */
interface BundlerInterface {

  /**
   * Identifies sources withing environment.
   *
   * The same identity can be used by multiple sources within environment.
   * This is the main characteristic that used for bundle multiple content.
   *
   * E.g.: Source A and Source B can return both ID: 'foo'. In context of
   * bundler it means that those documents both about 'foo', but they can have
   * different attributes, for example, Source A is in Russian but Source B in
   * English. This makes them a 'foo' bundle with a different language
   * variations.
   *
   * More complex example. Let's assume we have content about 'Drupal Hooks' in
   * two different languages 'ru' and 'en', also it has versions for Drupal 8,
   * Drupal 9 and Drupal 10. This is a good case for a single bundle. In that
   * case, all those documents can return ID 'hooks', and each document will
   * have its own additional attributes:
   *
   * - ID: hooks
   * - Variations:
   * -- language: ru, drupal: 8
   * -- language: en, drupal: 8
   * -- language: ru, drupal: 9
   * -- language: en, drupal: 9
   * -- language: ru, drupal: 10
   * -- language: en, drupal: 10
   *
   * In that scenario, later on, you will be able to get all variants of the
   * same content for different purposes, for example, all russian versions or
   * all Drupal 10 versions based on attributes.
   */
  public function bundle(SourceInterface $source): BundlerResultInterface;

}
