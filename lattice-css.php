<?php

/**
 * Lattice CSS — package implementing the Digitalis CSS System.
 *
 * Practice-level CSS architecture. Boot file for the PHP-side token parser.
 * SCSS lives under scss/ and is compiled by SASSY.
 */

if (!defined('WPINC')) die;
if (defined('LATTICE_CSS_VERSION')) return;

define('LATTICE_CSS_VERSION', '0.8.0');
define('LATTICE_CSS_PATH',    plugin_dir_path(__FILE__));

require_once LATTICE_CSS_PATH . 'include/tokens.class.php';
require_once LATTICE_CSS_PATH . 'include/editor-palette.class.php';

\Lattice\Css\Editor_Palette::register();
