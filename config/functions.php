<?php

function getFullDomainUrl(bool $sufix = true)
{
  if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')
    && (!isset($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] != 443)
  ) {
    $protocol = 'http://';
  } else {
    $protocol = 'https://';
  }

  $host = $_SERVER['HTTP_HOST'];
  $fullDomainUrl = $protocol . $host . ($sufix ? '/' : '');

  return $fullDomainUrl;
}

/**
 * Check 
 * @param array $keys
 * 
 * @return string
 */
function prepareUrlForQuery(array $keys): string
{
  $baseUrl = getFullDomainUrl(false) . $_SERVER['REQUEST_URI'];
  $parsedUrl = parse_url($baseUrl);

  if (!isset($parsedUrl['query'])) {
    return $baseUrl . '?';
  }

  // Parse existing query string into key-value pairs
  parse_str($parsedUrl['query'], $queryParams);

  if (!empty($keys)) {
    foreach ($keys as $key) {
      if (isset($queryParams[$key])) {
        unset($queryParams[$key]);
      }
    }
  }

  // Rebuild the query string
  $newQueryString = http_build_query($queryParams);

  return getFullDomainUrl(false) . $parsedUrl['path'] . '?' . (empty($newQueryString) ? '' : $newQueryString . '&');
}

function addQueryToUrl(string $key, string $value): string
{
  return prepareUrlForQuery(['page', 'page_action']) . $key . '=' . $value;
}
