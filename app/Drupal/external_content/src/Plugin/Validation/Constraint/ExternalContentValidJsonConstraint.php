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
final class ExternalContentValidJsonConstraint extends Constraint {

  /**
   * {@selfdoc}
   */
  public string $invalidJsonMessage = 'The supplied string is not a valid JSON value.';

  /**
   * Skips the empty value.
   *
   * If set to TRUE, NULL as a value will be treated as a valid value. It can be
   * helpful for cases where value is optional and validation is triggered for
   * it.
   */
  public bool $skipEmptyValue = FALSE;

  /**
   * Constructs a new ExternalContentValidJsonConstraint instance.
   */
  public function __construct(
    mixed $options = NULL,
    ?array $groups = NULL,
    mixed $payload = NULL,
  ) {
    $this->skipEmptyValue = $options['skipEmptyValue'] ?? $this->skipEmptyValue;

    parent::__construct($options, $groups, $payload);
  }

}
