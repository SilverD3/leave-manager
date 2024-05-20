<?php

function getFullDomainUrl()
{
  if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')
    && (!isset($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] != 443)
  ) {
    $protocol = 'http://';
  } else {
    $protocol = 'https://';
  }

  $host = $_SERVER['HTTP_HOST'];
  $fullDomainUrl = $protocol . $host . '/';

  return $fullDomainUrl;
}
