<?php

declare(strict_types=1);

namespace Drupal\niklan\Console\Log;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleLogger implements LoggerInterface {

  public function __construct(
    private LoggerInterface $innerLogger,
    private OutputInterface $output,
  ) {}

  public function emergency($message, array $context = []): void {
    $this->log('emergency', $message, $context);
  }

  public function log($level, $message, array $context = []): void {
    $this->innerLogger->log($level, $message, $context);

    $formatted = $this->format($level, $message, $context);

    if (\in_array($level, ['error', 'critical', 'alert', 'emergency'], TRUE)) {
      $this->output->writeln("<error>$formatted</error>");
    }
    elseif ($level === 'warning') {
      $this->output->writeln("<comment>$formatted</comment>");
    }
    else {
      $this->output->writeln($formatted);
    }
  }

  public function alert($message, array $context = []): void {
    $this->log('alert', $message, $context);
  }

  public function critical($message, array $context = []): void {
    $this->log('critical', $message, $context);
  }

  public function error($message, array $context = []): void {
    $this->log('error', $message, $context);
  }

  public function warning($message, array $context = []): void {
    $this->log('warning', $message, $context);
  }

  public function notice($message, array $context = []): void {
    $this->log('notice', $message, $context);
  }

  public function info($message, array $context = []): void {
    $this->log('info', $message, $context);
  }

  public function debug($message, array $context = []): void {
    $this->log('debug', $message, $context);
  }

  private function format(string $level, string $message, array $context): string {
    return \sprintf('[%s] %s %s', \strtoupper($level), $message, \json_encode($context));
  }

}
