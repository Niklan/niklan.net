<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme\PropsAlter;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Site\Settings;
use Drupal\app_contract\Contract\Console\Git;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PageFooterPropsAlter implements ContainerInjectionInterface {

  public function __construct(
    private Git $git,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    $git = $container->get(Git::class);
    \assert($git instanceof Git);

    return new self($git);
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

  public function __invoke(array $props): array {
    $external_content_directory = Settings::get('external_content_directory');
    \assert(\is_string($external_content_directory));
    $external_content_repository_url = Settings::get('external_content_repository_url');
    \assert(\is_string($external_content_repository_url));
    $website_repository_url = Settings::get('website_repository_url');
    \assert(\is_string($website_repository_url));

    $props['versions'] = [
      'content' => $this->buildVersionInfo(
        repository_path: $external_content_directory,
        repository_url: $external_content_repository_url,
      ),
      'website' => $this->buildVersionInfo(
        repository_path: \DRUPAL_ROOT,
        repository_url: $website_repository_url,
      ),
    ];

    return $props;
  }

}
