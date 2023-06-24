<?php

namespace Drupal\helper\Environment;

class PantheonEnvironment extends DefaultEnvironment {

  /**
   * {@inheritdoc}
   */
  public const ENVIRONMENT_NAME = 'PANTHEON_ENVIRONMENT';

  /**
   * {@inheritdoc}
   */
  public static function isPreview(): bool {
    return static::isMultidev() || parent::isPreview();
  }

  /**
   * Determine if this is a Pantheon Multidev environment.
   *
   * @return bool
   *   TRUE if this is a Pantheon Multidev environment.
   */
  public static function isMultidev(): bool {
    return !in_array(static::getEnvironment(), ['dev', 'test', 'live'], TRUE) && !Environment::isCi() && !Environment::isLocal();
  }

}
