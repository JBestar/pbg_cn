<?php
/**
 * Load project-root .env into getenv / $_ENV / $_SERVER (powerball-style).
 */
function pbg_load_env($path = null)
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $file = $path ?: (dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');
    if (!is_file($file) || !is_readable($file)) {
        return;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || $line[0] === ';') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if ($name === '') {
            continue;
        }
        // strip inline comment after unquoted value:  foo = bar # note
        if ($value !== '' && ($value[0] === "'" || $value[0] === '"')) {
            $q = $value[0];
            if (substr($value, -1) === $q && strlen($value) >= 2) {
                $value = substr($value, 1, -1);
            }
        } else {
            if (($hash = strpos($value, ' #')) !== false) {
                $value = rtrim(substr($value, 0, $hash));
            }
            if (($hash = strpos($value, ' ;')) !== false) {
                $value = rtrim(substr($value, 0, $hash));
            }
        }
        if (getenv($name) === false) {
            putenv("$name=$value");
        }
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

/**
 * @param mixed $default
 * @return mixed
 */
function pbg_env($key, $default = null)
{
    pbg_load_env();
    $value = getenv($key);
    if ($value === false && array_key_exists($key, $_ENV)) {
        $value = $_ENV[$key];
    }
    if ($value === false || $value === null || $value === '') {
        return $default;
    }
    switch (strtolower((string)$value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }
    return $value;
}
