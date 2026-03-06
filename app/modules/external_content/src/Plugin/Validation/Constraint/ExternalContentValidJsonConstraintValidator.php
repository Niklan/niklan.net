<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class ExternalContentValidJsonConstraintValidator extends ConstraintValidator {

  #[\Override]
  public function validate(mixed $value, Constraint $constraint): void {
    \assert($constraint instanceof ExternalContentValidJsonConstraint);

    if (\is_string($value) && \json_validate($value)) {
      return;
    }

    $this->addInvalidJsonViolation($constraint);
  }

  private function addInvalidJsonViolation(ExternalContentValidJsonConstraint $constraint): void {
    $this->context->buildViolation($constraint->invalidJsonMessage)->addViolation();
  }

}
