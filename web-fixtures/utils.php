<?php

function html_escape_value(string $data): string
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Dumps a value for consumption in tests
 *
 * The output format does not use any HTML special chars in boilerplate, to make
 * it simpler to write assertion on the HTML content generated based on it (no change
 * in the boilerplate parts during escaping).
 *
 * @param mixed $value
 *
 * @return string
 */
function mink_dump($value)
{
    if (null === $value) {
        return 'null';
    }

    if (is_string($value)) {
        return sprintf('`%s`', $value);
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_int($value) || is_float($value)) {
        return var_export($value, true);
    }

    if (is_array($value)) {
        if (empty($value)) {
            return 'array()';
        }

        $output = array('array(');

        foreach ($value as $k => $v) {
            $output[] = sprintf('%s = %s,', $k, str_replace("\n", "\n  ", mink_dump($v)));
        }

        return implode("\n  ", $output)."\n)";
    }

    return gettype($value); // We don't have full dumping of resource and object, as we don't need them
}
