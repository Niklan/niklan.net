<?php

declare(strict_types=1);

namespace Drupal\Tests\external_content\Unit\Pipeline;

use Drupal\external_content\Contract\Pipeline\PipelineConfig;
use Drupal\external_content\Contract\Pipeline\PipelineContext;
use Drupal\external_content\Contract\Pipeline\PipelineStage;
use Drupal\external_content\Pipeline\NullPipelineContext;
use Drupal\external_content\Pipeline\SequentialPipeline;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * @covers \Drupal\external_content\Pipeline\SequentialPipeline
 * @group external_content
 */
final class SequentialPipelineTest extends UnitTestCase {

  public function testPipelineExecution(): void {
    $config_1 = $this->prophesize(PipelineConfig::class)->reveal();
    $config_2 = $this->prophesize(PipelineConfig::class)->reveal();

    $input_context = $this->prophesize(PipelineContext::class)->reveal();
    $middle_context = $this->prophesize(PipelineContext::class)->reveal();
    $result_context = $this->prophesize(PipelineContext::class)->reveal();

    $stage_1 = $this->prophesize(PipelineStage::class);
    $stage_1->process($input_context, $config_1)->willReturn($middle_context);
    $stage_1 = $stage_1->reveal();

    $stage_2 = $this->prophesize(PipelineStage::class);
    $stage_2->process($middle_context, $config_2)->willReturn($result_context);
    $stage_2 = $stage_2->reveal();

    $pipeline = new SequentialPipeline();
    $pipeline->addStage($stage_1, $config_1);
    $pipeline->addStage($stage_2, $config_2);
    $result = $pipeline->run($input_context);

    $this->assertSame($result_context, $result);
  }

  public function testStageExceptionHandling(): void {
    $logger = $this->prophesize(LoggerInterface::class);
    $logger->error(Argument::exact('Stage failed: Test exception'))->shouldBeCalled();
    $logger = $logger->reveal();

    $stage = $this->prophesize(PipelineStage::class);
    $stage->process(Argument::any(), Argument::any())->willThrow(new \Exception('Test exception'));

    $pipeline = new SequentialPipeline($logger);
    $pipeline->addStage($stage->reveal());
    $pipeline->run(new NullPipelineContext());
  }

}
