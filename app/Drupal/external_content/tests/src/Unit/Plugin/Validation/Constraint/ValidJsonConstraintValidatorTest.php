<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Plugin\Validation\Constraint;

use Drupal\external_content\Plugin\Validation\Constraint\ExternalContentValidJsonConstraint;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Validator\Validation;

/**
 * Provides a test for JSON validation constraint.
 *
 * @covers \Drupal\external_content\Plugin\Validation\Constraint\ExternalContentValidJsonConstraintValidator
 * @covers \Drupal\external_content\Plugin\Validation\Constraint\ExternalContentValidJsonConstraint
 * @group external_content
 */
final class ValidJsonConstraintValidatorTest extends UnitTestCase {

  /**
   * {@selfdoc}
   *
   * @dataProvider validateData
   */
  public function testValidate(mixed $input, int $expected_violations): void {
    $constraint = new ExternalContentValidJsonConstraint();
    $validator = Validation::createValidator();

    $violations = $validator->validate($input, $constraint);
    self::assertCount($expected_violations, $violations);
  }

  /**
   * {@selfdoc}
   */
  public function validateData(): \Generator {
    yield 'Not a JSON string' => [
      'input' => 'random string',
      'expected_violations' => 1,
    ];

    yield 'Array' => [
      'input' => ['foo' => 'bar'],
      'expected_violations' => 1,
    ];

    yield 'Object' => [
      'input' => new \stdClass(),
      'expected_violations' => 1,
    ];

    yield 'Valid JSON' => [
      'input' => '{"foo": "bar"}',
      'expected_violations' => 0,
    ];

    yield 'NULL input' => [
      'input' => NULL,
      'expected_violations' => 1,
    ];
  }

}
