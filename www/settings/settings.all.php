<?php

// phpcs:ignoreFile

/**
 * @file
 * Global settings file with common includes for all environments.
 *
 * This file is included by each environment-specific settings file
 * (settings.dev.php, settings.demo.php, settings.production.php).
 *
 * $settings_dir is defined in web/sites/default/settings.php.
 */

// Feature-specific settings.
include __DIR__ . '/openai/settings.php';

if (file_exists($settings_dir . '/settings.overrides.php')) {
  include $settings_dir . '/settings.overrides.php';
}
