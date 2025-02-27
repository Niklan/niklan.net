<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Pipeline;

use Drupal\external_content\Contract\Pipeline\Config;
use Drupal\external_content\Contract\Pipeline\Context;
use Drupal\external_content\Contract\Pipeline\Stage;
use Drupal\external_content\Pipeline\DefaultPipeline;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers DefaultPipeline
 * @group external_content
 */
final class DefaultPipelineTest extends UnitTestCase {

  public function testPipelineExecution(): void {
    $logger = $this->prophesize(LoggerInterface::class)->reveal();

    $config_1 = $this->prophesize(Config::class)->reveal();
    $config_2 = $this->prophesize(Config::class)->reveal();

    $input_context = $this->prophesize(Context::class)->reveal();
    $middle_context = $this->prophesize(Context::class)->reveal();
    $result_context = $this->prophesize(Context::class)->reveal();

    $stage_1 = $this->prophesize(Stage::class);
    $stage_1->process($input_context, $config_1)->willReturn($middle_context);
    $stage_1 = $stage_1->reveal();

    $stage_2 = $this->prophesize(Stage::class);
    $stage_2->process($middle_context, $config_2)->willReturn($result_context);
    $stage_2 = $stage_2->reveal();

    $pipeline = new DefaultPipeline($logger);
    $pipeline->addStage($stage_1, $config_1);
    $pipeline->addStage($stage_2, $config_2);
    $result = $pipeline->run($input_context);

    $this->assertSame($result_context, $result);
  }

  public function testLoggerInjection(): void {
    $logger = $this->prophesize(LoggerInterface::class)->reveal();

    $logger_aware_stage = $this->prophesize(Stage::class);
    $logger_aware_stage->willImplement(LoggerAwareInterface::class);
    $logger_aware_stage->setLogger($logger)->shouldBeCalled();
    $logger_aware_stage = $logger_aware_stage->reveal();

    $pipeline = new DefaultPipeline($logger);
    $pipeline->addStage($logger_aware_stage, NULL);
  }

}
