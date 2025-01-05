<?php

namespace Behat\Mink\Tests\Driver\Util;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FixturesKernel implements HttpKernelInterface
{
    public const WEB_FIXTURES_DIR = __DIR__ . '/../web-fixtures';
    public const KERNEL_FIXTURES_DIR = __DIR__ . '/../http-kernel-fixtures';

    public function handle(Request $request, $type = 1 /* self::MAIN_REQUEST */ , $catch = true): Response
    {
        $this->prepareSession($request);

        $response = $this->handleFixtureRequest($request);

        $this->saveSession($request, $response);
        $response->prepare($request);

        return $response;
    }

    private function handleFixtureRequest(Request $request): Response
    {
        $fixturesDir = realpath(self::WEB_FIXTURES_DIR);
        $overwriteDir = realpath(self::KERNEL_FIXTURES_DIR);

        require_once $fixturesDir . '/utils.php';

        $file = $request->getPathInfo();

        $path = file_exists($overwriteDir . $file) ? $overwriteDir . $file : $fixturesDir . $file;

        /** @var Response|null $resp */
        $resp = null;

        ob_start();
        require $path;
        $content = ob_get_clean();
        \assert($content !== false);

        if ($resp instanceof Response) {
            if ('' === $resp->getContent()) {
                $resp->setContent($content);
            }

            return $resp;
        }

        return new Response($content);
    }

    private function prepareSession(Request $request): void
    {
        $session = new Session(new MockFileSessionStorage());
        $request->setSession($session);

        $cookies = $request->cookies;

        $value = $cookies->get($session->getName());
        if ($value !== null) {
            $session->setId($value);
        } else {
            $session->migrate(false);
        }
    }

    private function saveSession(Request $request, Response $response): void
    {
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        if ($session->isStarted()) {
            $session->save();

            $params = session_get_cookie_params();

            $cookie = Cookie::create(
                $session->getName(),
                $session->getId(),
                0 === $params['lifetime']
                    ? 0
                    : time() + $params['lifetime'],
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );

            $response->headers->setCookie($cookie);
        }
    }
}
