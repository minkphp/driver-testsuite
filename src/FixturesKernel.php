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
    public function handle(
        Request $request,
        $type = 1/*HttpKernelInterface::MASTER_REQUEST or MAIN_REQUEST*/,
        $catch = true
    ): Response {
        $this->prepareSession($request);

        $response = $this->handleFixtureRequest($request);

        $this->saveSession($request, $response);
        $response->prepare($request);

        return $response;
    }

    private function handleFixtureRequest(Request $request): Response
    {
        $fixturesDir = realpath(__DIR__.'/../web-fixtures');
        $overwriteDir = realpath(__DIR__.'/../http-kernel-fixtures');

        /** @psalm-suppress UnresolvableInclude */
        require_once $fixturesDir . '/utils.php';

        $file = $request->getPathInfo();

        $path = file_exists($overwriteDir.$file) ? $overwriteDir.$file : $fixturesDir.$file;

        $response = null;

        ob_start();
        /** @psalm-suppress UnresolvableInclude */
        require $path;
        $content = ob_get_clean();

        /** @psalm-suppress TypeDoesNotContainType */
        if ($response instanceof Response) {
            if ('' === $response->getContent()) {
                $response->setContent($content);
            }

            return $response;
        }

        return new Response($content);
    }

    private function prepareSession(Request $request): void
    {
        $session = new Session(new MockFileSessionStorage());
        $request->setSession($session);

        $cookies = $request->cookies;

        $sessionName = $session->getName();
        if ($cookies->has($sessionName)) {
            $id = (string) $cookies->get($sessionName);
            $session->setId($id);
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
                0 === $params['lifetime'] ? 0 : time() + $params['lifetime'],
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );

            $response->headers->setCookie($cookie);
        }
    }
}
