<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

/**
 * Represents a bundler compare result with reason.
 */
interface BundlerCompareResultReasonInterface {

  /**
   * Gets the reason why it should be bundled.
   *
   * Expected very simplistic machine-alike string. E.g.: 'language'.
   */
  public function getReason(): string;

  /**
   * Gets the unique reason ID.
   *
   * This should unique identify result within a reason group. E.g.: 'en', 'ru'.
   * In a reason 'language' a reason ID 'en' will helps easily identify why it
   * was bundled.
   */
  public function getReasonId(): string;

}
