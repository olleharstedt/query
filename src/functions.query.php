<?php

namespace Query;

use Exception;
use RuntimeException;

/**
 * @see https://stackoverflow.com/questions/16027102/get-domain-name-from-full-url
 * @psalm-mutation-free
 */
function get_domain(string $url): string|bool
{
    $pieces = parse_url($url);
    if (!is_array($pieces)) {
        throw new RuntimeException("Not array from parse_url");
    }
    /** @psalm-suppress PossiblyUndefinedArrayOffset */
    $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
        return $regs['domain'];
    }
    return false;
}

/**
 * @see https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
 * @psalm-mutation-free
 */
function ends_with(string $haystack, string $needle): bool {
    $length = strlen($needle);
    if(!$length) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}

/* TODO: Rewrite as pipe
function getJsonFromApi(string $query, array $config): array
{
    $useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
    $ch = curl_init("");
    $params = [
        'key' => $config['key'],
        'cx'  => $config['cx'],
        'q'   => $query
    ];

    $url = "https://www.googleapis.com/customsearch/v1?" . http_build_query($params);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent); // set user agent
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if (is_string($content) ) {
        return json_decode($content, true);
    } else {
        throw new Exception($error);
    }
}
 */

/**
 * @psalm-mutation-free
 */
function pipe(): Pipeline
{
    $args = func_get_args();
    return new Pipeline($args);
}

/**
 * @see https://zerowp.com/split-php-array-in-x-equal-number-of-elements/
 */
function split_array(array $input_array, int $size, bool $preserve_keys = false): array
{
    $nr = (int) ceil(count($input_array) / $size);
    if ($nr > 0) {
        return array_chunk($input_array, $nr, $preserve_keys);
    }
    return $input_array;
}

function pick_first(?iterable $things): mixed
{
    if (empty($things)) {
        return null;
    }
    foreach ($things as $_) {
        return $_;
    }
    return null;
}

function abort_at_empty(mixed $payload): mixed
{
    if (empty($payload)) {
        throw new ReturnEarlyException(null);
    } else {
        return $payload;
    }
}

