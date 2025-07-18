<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Site\Settings;
use Drupal\niklan\Console\Process\GitInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessLaszloPageFooter implements ContainerInjectionInterface {

  public function __construct(
    private GitInterface $git,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(GitInterface::class),
    );
  }

  private function addVersions(array &$variables): void {
    $external_content_directory = Settings::get('external_content_directory');
    \assert(\is_string($external_content_directory));
    $external_content_repository_url = Settings::get('external_content_repository_url');
    \assert(\is_string($external_content_repository_url));
    $website_repository_url = Settings::get('website_repository_url');
    \assert(\is_string($website_repository_url));

    $variables['versions']['content'] = $this->buildVersionInfo(
      repository_path: $external_content_directory,
      repository_url: $external_content_repository_url,
    );
    $variables['versions']['website'] = $this->buildVersionInfo(
      repository_path: \DRUPAL_ROOT,
      repository_url: $website_repository_url,
    );
  }

  private function buildVersionInfo(string $repository_path, string $repository_url): array {
    $commit_id = $this->getCommitId($repository_path);
    $version = $this->getVersion($repository_path);

    return [
      'commit_id' => $commit_id,
      'commit_id_short' => \substr($commit_id, 0, 7),
      'version' => $version,
      'source_url' => "$repository_url/tree/$commit_id",
    ];
  }

  private function getVersion(string $repository_path): string {
    $process = $this->git->describeTags($repository_path);
    $process->run();

    return \rtrim($process->getOutput());
  }

  private function getCommitId(string $repository_path): string {
    $process = $this->git->getLastCommitId($repository_path);
    $process->run();
    $commit_id = $process->getOutput();
    $commit_id = \trim($commit_id);

    return \str_replace('"', '', $commit_id);
  }

  public function __invoke(array &$variables): void {
    $this->addVersions($variables);
  }

}
