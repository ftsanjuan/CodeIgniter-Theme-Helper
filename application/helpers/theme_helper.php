<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$GLOBALS['CI'] =& get_instance();

define('THEMES_PATH', 'themes/');
define('CURRENT_THEME', current_theme());
define('CURRENT_THEME_PATH', current_theme_path());

/**
 * Returns the current theme name
 */
function current_theme(){
  return $GLOBALS['CI']->config->item('theme');
}

/**
 * Returns the current theme's path
 */
function current_theme_path() {
  return THEMES_PATH . CURRENT_THEME . '/';
}

/**
 * Returns the path to a theme
 *
 * @param string $theme
 *  the name of the theme
 */
function theme_path($theme = NULL) {
  if (is_null($theme)) {
    $theme = CURRENT_THEME;
  }
  return THEMES_PATH . $theme . '/';
}

/**
 * Displays the requested content in the supplied layout and injects
 * any variables supplied to it.
 *
 * Also inserts layout names as css classes to be used for the template's body tag
 *
 * @param string $layout
 *    the name of the layout file to be used
 * @param string $content
 *    the content to be inserted into the template
 * @param array() $variables
 *    an associative array of variables to be made available
 *    in the template being rendered.
 *    array keys will become variable names and array values
 *    are variable values.
 * @param bool $pretranslated
 *    whether or not to use a pre-translated template file.
 *    defaults to: FALSE - (returns an English template)
 *    If set to TRUE, content template must exist in the
 *    content/{language} subfolder.
 *
 */
function theme($layout, $content, $variables, $pretranslated = FALSE) {
  // load any modules/models you might need in your template need below:
  // $this->load->model('Model');

  // retrieve the language code (language_abbr) from config
  // default to 'en'
  $lang = substr($GLOBALS['CI']->session->userdata('site_lang'), 0, 2);
  if ( empty($lang) ) {
    $lang = "en";
  }

  if ( $pretranslated && $lang !== "en" ) {
    $subfolder = "{$lang}/";
  }
  else {
    $subfolder = "";
  }

  // append classes to the <body> tags classes
  if ( isset($variables['classes']) ) {
    $variables['classes'] = "layout-{$layout} layout-{$lang} " . $variables['classes'];
  }
  else {
    $variables['classes'] = "layout-{$layout} layout-{$lang}";
  }

  // set page title (aka. 'section') as a body class
  if ( isset($variables['section']) ) {
    $section = strtolower($variables['section']);
    $variables['classes'] .= " page-{$section}";
  }

  // add language code to theme vars
  $variables['lang_code'] = $lang;

  // add aggregation vars to theme
  $aggregation = $GLOBALS['CI']->config->item('aggregation');
  $variables['aggregate_js'] = $aggregation->js->enabled;

  // get content template to be displayed and display it
  $variables['content'] = $GLOBALS['CI']->load->view("content/{$subfolder}{$content}", $variables, TRUE);
  $GLOBALS['CI']->load->view( CURRENT_THEME_PATH . "layouts/{$layout}", $variables);
}

/**
 * Retrieves the email template
 * any variables supplied to it.
 *
 * Also inserts layout names as css classes to be used for the template's body tag
 *
 * @param string $content
 *    the email content template to be rendtered
 * @param array() $variables
 *    an associative array of variables to be made available
 *    in the template being rendered.
 *    array keys will become variable names and array values
 *    are variable values.
 * @param bool $pretranslated
 *    whether or not to use a pre-translated template file.
 *    defaults to: FALSE - (returns an English template)
 *    If set to TRUE, content template must exist in the
 *    email/{language} subfolder.
 *
 */
function theme_email($content, $variables, $pretranslated = FALSE) {
  // load any modules/models you might need in your template need below:
  $GLOBALS['CI']->lang->load('translation');
  // $GLOBALS['CI']->load->model('user');

  // retrieve the language code (language_abbr) from config
  $lang = substr($GLOBALS['CI']->session->userdata('site_lang'), 0, 2);
  if ( $pretranslated && $lang !== "en" ) {
    $subfolder = "{$lang}/";
  }
  else {
    $subfolder = "";
  }

  // get content template to be displayed and display it
  return $GLOBALS['CI']->load->view("emails/{$subfolder}{$content}", $variables, TRUE);
}

/**
 * Retrieves and returns a particular in-page template
 *
 * @param string $template
 *    the template (partial) to be displayed
 *
 * @param array $variables
 *    An array of variables
 *
 * @return the template (partial) file's contents
 */
function render($template, $variables = NULL) {
  return $GLOBALS['CI']->load->view(CURRENT_THEME_PATH . "partials/{$template}", $variables);
}

/**
 * Returns an array of meta tags retrieved from the site config
 */
function render_metatags() {
  $GLOBALS['CI']->load->helper('html');
  $meta = $GLOBALS['CI']->config->item('metatags');
  $rendered = array();
  foreach($meta as $name => $content) {
    $rendered[] = array(
      'name' => $name,
      'content' => $content,
    );
  }
  return meta($rendered);
}

/**
 * Returns the appropriate path for an asset
 * for a particular language
 *
 * @param string $filename
 *    the filename of the asset file
 * @param string $folder
 *    the parent folder the asset file lives in
 *    defaults to 'img' (used for image files)
 * @param string $subfolder
 *    the subfolder the file is found in
 */
function asset_path($filename, $folder = "img", $subfolder = "") {
  // handle subfolder paths:
  // remove "en/" if it has been supplied
  $subfolder .= "/";
  $subfolder = str_replace("en/", "", $subfolder);

  if (ENVIRONMENT == 'development') {
    return base_url() . "{$folder}/{$subfolder}{$filename}";
  }
  else {
    $cdn = $GLOBALS['CI']->config->item('cdn');
    if ($cdn->enabled) {
      $host = $cdn->hosts[$cdn->active_host];
      $base = "https://" . $host->domain . '/';
    }
    else {
      $base = base_url();
    }
    return $base . "{$folder}/{$subfolder}{$filename}";
  }
}

/**
 * Alias function for FCPATH.
 * Returns the (local) path to the root of this CodeIgniter installation on the server
 */
function basepath() {
  return FCPATH;
}

/**
 * Returns an <img> tag for a particular image file
 *
 * @param string $filename
 *    the filename of the image
 * @param string $lang
 *    the language code for this image (en/fr)
 * @param string $alt
 *    the alt text to be used for the <img> tag
 * @param array(string) $classes
 *    class names to be used for the class attribute of this <img>
 *
 * @return string
 *    an html <img> tag
 */
function image($filename, $lang = "en", $args = array()) {
  if ( !isset($args['attributes']) ) {
    $args['attributes'] = array();
  }

  $args['attributes'] += array(
    'alt' => $filename,
  );

  $img = asset_path($filename, "img", $lang);

  // create properties string for the <img> tag
  $prop_string = "";
  foreach($args['attributes'] as $attr => $value) {
    $prop_string .= " {$attr}='{$value}'";
  }

  return "<img src='{$img}' {$prop_string} />";
}

/**
 * Returns an html <script> tag with the path to the js file
 *
 * @param string $filename
 *    the javascript file's filename
 * @param string $subfolder
 *    the subfolder the javascript file can be found in
 *
 * @return string
 *    an html <script> tag
 */
function script($filename, $subfolder = "en") {
  return "<script type='text/javascript' src='" . asset_path($filename, "js", $subfolder) . "'></script>";
}

/**
 * Returns current language code 'en' / 'fr'
 */
function langcode() {
  return $GLOBALS['CI']->lang->lang();
}

/**
 * Combines multiple files into a single text file.
 */
function aggregate($files = array(), $folder = '') {
  $compiled = "";
  foreach ($files as $file) {
    $contents = file_get_contents(FCPATH . "{$folder}/" . $file);

    // append a delimiter after each aggregrated file, as needed
    $safe_delimiter = "";
    if ( $folder == 'js' ) {
      $safe_delimiter = ";";
    }

    if ( substr($contents, -1, 1) != $safe_delimiter ) {
      $contents .= $safe_delimiter;
    }
    $compiled .=  $contents;
  }

  return $compiled;
}

/**
 * An wrapper for aggregate() supplying multiple .js files from a /js folder
 * @note: RUN ONLY IN DEVELOPMENT to precompile aggregated js
 * and avoid any performance issues in production
 */
function compress_scripts($files = array(), $output_file = "scripts.js", $force_rebuild = FALSE) {
  $config = $GLOBALS['CI']->config->item('aggregation');
  $basename = basename($output_file, ".js");
  $output_file = $basename;

  // build the target filename
  if ( $config->js->cache == TRUE && $config->js->cachestamped ) {
    $cachestamp = floor(time() / $config->js->cache_life);
    $output_file .= "-{$cachestamp}";
  }
  if ( $config->js->minify == TRUE ) {
    $output_file .= ".min";
  }
  $output_file .= ".js";
  $target_file = FCPATH . "js/{$output_file}";

  // build an aggregated file
  if ( $force_rebuild || !file_exists($target_file) || ($config->js->cache && (time() - filemtime($target_file) > $config->js->cache_life)) ) {
    // remove any .min.js from the filename

    $unminified_file = FCPATH . "js/" . basename(str_replace('.min.js', '.js', $target_file));
    file_put_contents($unminified_file, aggregate($files, 'js'));
    if ( $config->js->minify == TRUE ) {
      $target_file = minify_js($unminified_file, $target_file);
    }
  }

  return script(basename($target_file));
}

/**
 * Minifies js file
 *
 * @param $input (string)
 *   path to the file to be compressed
 *
 * @param $output (string)
 *   path to write the minified file
 *   defaults to the same as $input
 *
 * @note: REQUIRES: java, YUI Compressor / Closure Compiler
 * @note: If $output is not set, this will overwrite $input
 *
 */
function minify_js($input, $output = NULL) {
  if ( is_null($output) ) {
    $output = $input;
  }

  // ensure that java, yui paths are set in config.php
  $config = $GLOBALS['CI']->config->item('aggregation');
  $java = $config->js->libs->java;
  $minifier_path = $config->js->libs->{$config->js->active_minifier};

  if ( $config->js->active_minifier == 'yui' ) {
    $cmd = sprintf(
      '%s -jar %s --type js -o %s %s',
      escapeshellcmd($java),
      escapeshellarg($minifier_path),
      escapeshellarg($input),
      escapeshellarg($output)
    );
  }
  // defaults to closurecompiler
  else {
    $cmd = sprintf(
      "%s -jar %s --js %s --js_output_file %s --language_in ECMASCRIPT5",
      escapeshellcmd($java),
      escapeshellarg($minifier_path),
      escapeshellarg($input),
      escapeshellarg($output)
    );
  }

  // minify all the things!
  shell_exec($cmd);

  // return output path
  return $output;
}