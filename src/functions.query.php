<?php

// @see https://stackoverflow.com/questions/16027102/get-domain-name-from-full-url
function get_domain($url)
{
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : $pieces['path'];
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
        return $regs['domain'];
    }
    return false;
}

// @see https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
function ends_with( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}

function getGoogleFromQuery(string $query)
{
    return file_get_contents("https://google.com/search?q=$query");
}

function getJsonFromApi(string $query, $config): array
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
    if ($content ) {
        return json_decode($content, true);
    } else {
        throw new Exception($error);
    }
}
