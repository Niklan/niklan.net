<?php declare(strict_types = 1);

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
  public function testValidate(mixed $input, int $expected_violations, array $options): void {
    $constraint = new ExternalContentValidJsonConstraint($options);
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
      'options' => [],
    ];

    yield 'Array' => [
      'input' => ['foo' => 'bar'],
      'expected_violations' => 1,
      'options' => [],
    ];

    yield 'Object' => [
      'input' => new \stdClass(),
      'expected_violations' => 1,
      'options' => [],
    ];

    yield 'Valid JSON' => [
      'input' => '{"foo": "bar"}',
      'expected_violations' => 0,
      'options' => [],
    ];

    yield 'Skip empty value option' => [
      'input' => NULL,
      'expected_violations' => 0,
      'options' => [
        'skipEmptyValue' => TRUE,
      ],
    ];

    yield 'Do not skip empty value option' => [
      'input' => NULL,
      'expected_violations' => 1,
      'options' => [
        'skipEmptyValue' => FALSE,
      ],
    ];
  }

}
