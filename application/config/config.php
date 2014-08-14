<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Add the following keys to CodeIgniter $config in your config.php
 */

/*
|--------------------------------------------------------------------------
| Themes
|--------------------------------------------------------------------------
|
| Settings for themes
| Set your site's theme here.
|
*/
$config['theme'] = 'substrate_bootstrap';

/*
|--------------------------------------------------------------------------
| Site Metatags
|--------------------------------------------------------------------------
|
*/
$config['metatags'] = (object) array(
  'description' => "Website Description",
  'keywords' => "website keywords, more keywords",
);

/*
|--------------------------------------------------------------------------
| Scripts Aggregation settings
|--------------------------------------------------------------------------
|
*/
$config['aggregation'] = (object) array(
  'js' => (object) array(
    'enabled' => FALSE,
    'cache' => FALSE,
    'cache_life' => 600,
    'cachestamped' => TRUE,
    'minify' => TRUE,
    'active_minifier' => 'closurecompiler',
    'libs' => (object) array(
      'java' => '/usr/bin/java',
      'closurecompiler' => FCPATH . 'libs/closurecompiler/compiler.jar',
      'yui' => FCPATH . 'libs/yui/yuicompressor-2.4.8.jar',
    ),
  ),
);

/*
|--------------------------------------------------------------------------
| CDN settings
|--------------------------------------------------------------------------
|
*/
$config['cdn'] = (object) array(
  'enabled' => FALSE,
  'active_host' => 'cachefly',
  'hosts' =>  array(
    'cachefly' => (object) array(
      'domain' => 'DOMAIN.cachefly.net'
    ),
    'cloudfront' => (object) array(
      'domain' => 'DOMAIN.cloudfront.net',
    ),
  )
);