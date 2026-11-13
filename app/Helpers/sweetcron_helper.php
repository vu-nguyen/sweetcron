<?php

if (! function_exists('safe_external_url')) {
    function safe_external_url(?string $url): string
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) return '';
        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true) ? $url : '';
    }
}
