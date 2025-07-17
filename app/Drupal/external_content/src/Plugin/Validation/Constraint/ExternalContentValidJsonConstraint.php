<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

#[Constraint(
  id: self::ID,
  label: new TranslatableMarkup('Valid JSON'),
)]
final class ExternalContentValidJsonConstraint extends SymfonyConstraint {

  public const string ID = 'ExternalContentValidJson';
  public string $invalidJsonMessage = 'The supplied string is not a valid JSON value.';

}
