<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ExternalContentValidJson constraint.
 */
final class ValidJsonConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {
    \assert($constraint instanceof ValidJsonConstraint);

    // If values is not a string, doesn't even bother to check for validity, it
    // is clearly not a JSON.
    if (!\is_string($value)) {
      $this->addInvalidJsonViolation($constraint);

      return;
    }

    $result = \json_decode($value);

    // If JSON is invalid, it will return NULL.
    if (!\is_null($result)) {
      return;
    }

    // At this point anything else is invalid.
    $this->addInvalidJsonViolation($constraint);
  }

  /**
   * {@selfdoc}
   */
  private function addInvalidJsonViolation(ValidJsonConstraint $constraint): void {
    $this
      ->context
      ->buildViolation($constraint->invalidJsonMessage)
      ->addViolation();
  }

}
