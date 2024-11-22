<?php


if (!function_exists('appUrl')) {
    function appUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        return (request())->getUriForPath('/storage/'.$path);
    }
}

if (!function_exists('mediaAppUrl')) {
    function mediaAppUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return (request())->getUriForPath($path);
    }
}
