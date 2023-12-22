<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the ExternalContentValidJson constraint.
 */
final class ExternalContentValidJsonConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {
    \assert($constraint instanceof ExternalContentValidJsonConstraint);

    // Exit without violation is NULL is a valid value.
    if (\is_null($value) && $constraint->skipEmptyValue) {
      return;
    }

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
  private function addInvalidJsonViolation(ExternalContentValidJsonConstraint $constraint): void {
    $this
      ->context
      ->buildViolation($constraint->invalidJsonMessage)
      ->addViolation();
  }

}
