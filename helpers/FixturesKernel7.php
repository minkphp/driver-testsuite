<?php

namespace Behat\Mink\Tests\Driver\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/AbstractFixturesKernel.php';

class FixturesKernel extends FixturesKernelAbstract
{
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true): Response
    {
        return $this->doHandle(...func_get_args());
    }
}
