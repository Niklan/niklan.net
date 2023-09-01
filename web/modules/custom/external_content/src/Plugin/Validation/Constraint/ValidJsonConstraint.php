<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a JSON validation for external content.
 *
 * @Constraint(
 *   id = "ExternalContentValidJson",
 *   label = @Translation("JSON validate"),
 * )
 */
final class ValidJsonConstraint extends Constraint {

  /**
   * {@selfdoc}
   */
  public string $invalidJsonMessage = 'The supplied string is not a valid JSON value.';

}
