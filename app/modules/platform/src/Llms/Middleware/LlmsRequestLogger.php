<?php

declare(strict_types=1);

namespace Drupal\app_platform\Llms\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[AutoconfigureTag('http_middleware', ['priority' => 210])]
final readonly class LlmsRequestLogger implements HttpKernelInterface {

  public function __construct(
    private HttpKernelInterface $httpKernel,
    #[Autowire(service: 'logger.channel.app_platform.llms')]
    private LoggerInterface $logger,
  ) {}

  #[\Override]
  public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = TRUE): Response {
    $response = $this->httpKernel->handle($request, $type, $catch);

    if ($type === self::MAIN_REQUEST && $request->query->get('_wrapper_format') === 'llms') {
      $this->logger->info('LLMS request', [
        'path' => $request->getPathInfo(),
        'user_agent' => $request->headers->get('User-Agent', ''),
        'referer' => $request->headers->get('Referer', ''),
        'status' => $response->getStatusCode(),
        'cache' => $response->headers->get('X-Drupal-Cache', 'NONE'),
      ]);
    }

    return $response;
  }

}
