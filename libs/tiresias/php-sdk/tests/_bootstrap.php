<?php
// This is global bootstrap for autoloading

date_default_timezone_set('Europe/Helsinki');

// Pre-load all sdk classes.
require_once(dirname(__FILE__) . '/../src/config.inc.php');

// Configure API, Web Hooks, and OAuth client to use Mock server when testing.
TiresiasApiRequest::$baseUrl = 'http://localhost:3000';
TiresiasOAuthClient::$baseUrl = 'http://localhost:3000';
TiresiasHttpRequest::$baseUrl = 'http://localhost:3000';
