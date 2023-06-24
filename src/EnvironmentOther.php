<?php

namespace Davereid\DrupalEnvironment;

class Test
{

    public const LOCAL = 'local';
    public const TUGBOAT = 'tugboat';
    public const PROD = 'prod';
    public const TEST = 'test';
    public const DEV = 'dev';
    public const LIVE = 'live';

    /**
     * Determine if this is a production environment.
     *
     * @return bool
     *   TRUE if this is a production environment.
     */
    public static function isProduction(): bool
    {
        return (bool)static::getEnvironment() === 'prod';
    }

    /**
     * Determine if this is a staging/test environment.
     *
     * @return bool
     *   TRUE if this is a staging/test environment.
     */
    public static function isStaging(): bool
    {
        return (bool)static::getEnvironment() === 'test';
    }

    /**
     * Determine if this is a development environment.
     *
     * @return bool
     *   TRUE if this is a development environment.
     */
    public static function isDev(): bool
    {
        return (bool)static::getEnvironment() === 'dev';
    }

    /**
     * Determine if this is a Lando environment.
     *
     * @return bool
     *   TRUE if this is a Lando environment.
     */
    public static function isLando(): bool
    {
        return (bool)static::getEnvironment() === 'LANDO';
    }

    /**
     * Get the environment_indicator config for this environment.
     *
     * @return array|null
     *   The config.
     */
    public static function getIndicatorConfig(): ?array
    {
        switch (static::getEnvironment()) {

            case static::LOCAL:
                return [
                    'name' => 'Local',
                    // Soft Black.
                    'bg_color' => '#000300',
                    // Warm Neutral.
                    'fg_color' => '#FFF4E7',
                ];
            default:
                if (static::isProduction()) {
                    return [
                        'name' => 'Prod',
                        // Red clay.
                        'bg_color' => '#B01116',
                        // Warm Neutral.
                        'fg_color' => '#FFF4E7',
                    ];
                } else {

                }


        }
        if (static::isProduction()) {

        }
        $config_map = [
            static::LOCAL => [
                'name' => 'Local',
                // Soft Black.
                'bg_color' => '#000300',
                // Warm Neutral.
                'fg_color' => '#FFF4E7',
            ],
            static::TUGBOAT => [
                'name' => 'Tugboat',
                // Atlanta Blue.
                'bg_color' => '#145996',
                // Cool Neutral.
                'fg_color' => '#F2F7F5',
            ],
            static::DEV => [
                'name' => 'Dev',
                // Live Oak.
                'bg_color' => '#08473D',
                // Cool Neutral.
                'fg_color' => '#F2F7F5',
            ],
            static::TEST => [
                'name' => 'Test',
                // Macon Red.
                'bg_color' => '#FA6933',
                // Warm Neutral.
                'fg_color' => '#FFF4E7',
            ],
            static::PROD => [
                'name' => 'Prod',
                // Red clay.
                'bg_color' => '#B01116',
                // Warm Neutral.
                'fg_color' => '#FFF4E7',
            ],
            static::LIVE => [
                'name' => 'Prod',
                // Red clay.
                'bg_color' => '#B01116',
                // Warm Neutral.
                'fg_color' => '#FFF4E7',
            ],
        ];
        $env = Environment::getEnvironment();
        return $config_map[$env] ?? null;
    }

}
