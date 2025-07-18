<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ExternalContentValidJsonConstraintValidator extends ConstraintValidator {

  #[\Override]
  public function validate(mixed $value, Constraint $constraint): void {
    \assert($constraint instanceof ExternalContentValidJsonConstraint);
    // If values is not a string, doesn't even bother to check for validity, it
    // is clearly not a JSON.
    if (\is_null($value) || !\is_string($value)) {
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

  private function addInvalidJsonViolation(ExternalContentValidJsonConstraint $constraint): void {
    $this->context->buildViolation($constraint->invalidJsonMessage)->addViolation();
  }

}
