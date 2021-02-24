<?php

declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Util;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FixturesKernel extends AbstractBrowser implements HttpKernelInterface
{
    public function handle(Request $request, int $type = self::MASTER_REQUEST, bool $catch = true): Response
    {
        $this->prepareSession($request);

        $response = $this->handleFixtureRequest($request);

        $this->saveSession($request, $response);
        $response->prepare($request);

        return $response;
    }

    private function handleFixtureRequest(Request $request)
    {
        $fixturesDir = realpath(__DIR__.'/../web-fixtures');
        $overwriteDir = realpath(__DIR__.'/../http-kernel-fixtures');

        require_once $fixturesDir . '/utils.php';

        $file = $request->getPathInfo();

        $path = file_exists($overwriteDir.$file) ? $overwriteDir.$file : $fixturesDir.$file;

        $resp = null;

        ob_start();
        require $path;
        $content = ob_get_clean();

        if ($resp instanceof Response) {
            if ('' === $resp->getContent()) {
                $resp->setContent($content);
            }

            return $resp;
        }

        return new Response($content);
    }

    private function prepareSession(Request $request)
    {
        $session = new Session(new MockFileSessionStorage());
        $request->setSession($session);

        $cookies = $request->cookies;

        if ($cookies->has($session->getName())) {
            $session->setId($cookies->get($session->getName()));
        } else {
            $session->migrate(false);
        }
    }

    private function saveSession(Request $request, Response $response)
    {
        $session = $request->getSession();
        if ($session && $session->isStarted()) {
            $session->save();

            $params = session_get_cookie_params();

            if (method_exists('Symfony\Component\HttpFoundation\Cookie', 'create')) {
                $cookie = Cookie::create($session->getName(), $session->getId(), 0 === $params['lifetime'] ? 0 : time() + $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            } else {
                $cookie = new Cookie($session->getName(), $session->getId(), 0 === $params['lifetime'] ? 0 : time() + $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }

            $response->headers->setCookie($cookie);
        }
    }

    protected function doRequest($request)
    {
        /** @var BrowserKitRequest $request */
        $response = $this->handle(Request::create(
            $request->getUri(),
            $request->getMethod(),
            $request->getParameters(),
            $request->getCookies(),
            $request->getFiles(),
            $request->getServer(),
            $request->getContent()
        ));

        return new BrowserKitResponse(
            $response->getContent(),
            $response->getStatusCode(),
            $response->headers->allPreserveCase()
        );
    }
}
