<?php

namespace Behat\Mink\Tests\Driver\Util;

use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/AbstractFixturesKernel.php';

class FixturesKernel extends FixturesKernelAbstract
{
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return call_user_func_array(array($this, 'doHandle'), func_get_args());
    }
}
