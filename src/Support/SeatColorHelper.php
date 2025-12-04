<?php

namespace Przwl\CineReserve\Support;

class SeatColorHelper
{
    /**
     * Tailwind color palette - RGB values
     * All colors are defined here for easy maintenance
     */
    protected static array $colorPalette = [
        'blue' => [
            '400' => 'rgb(96, 165, 250)',
            '500' => 'rgb(59, 130, 246)',
            '600' => 'rgb(37, 99, 235)',
            '700' => 'rgb(29, 78, 216)',
            '800' => 'rgb(30, 64, 175)',
            '900' => 'rgb(30, 58, 138)',
        ],
        'emerald' => [
            '400' => 'rgb(52, 211, 153)',
            '500' => 'rgb(16, 185, 129)',
            '600' => 'rgb(5, 150, 105)',
            '700' => 'rgb(4, 120, 87)',
            '800' => 'rgb(6, 95, 70)',
            '900' => 'rgb(6, 78, 59)',
        ],
        'amber' => [
            '400' => 'rgb(251, 191, 36)',
            '500' => 'rgb(245, 158, 11)',
            '600' => 'rgb(217, 119, 6)',
            '700' => 'rgb(180, 83, 9)',
            '800' => 'rgb(146, 64, 14)',
            '900' => 'rgb(120, 53, 15)',
        ],
        'gray' => [
            '400' => 'rgb(156, 163, 175)',
            '500' => 'rgb(107, 114, 128)',
            '600' => 'rgb(75, 85, 99)',
            '700' => 'rgb(55, 65, 81)',
            '800' => 'rgb(31, 41, 55)',
            '900' => 'rgb(17, 24, 39)',
        ],
        'red' => [
            '400' => 'rgb(248, 113, 113)',
            '500' => 'rgb(239, 68, 68)',
            '600' => 'rgb(220, 38, 38)',
            '700' => 'rgb(185, 28, 28)',
            '800' => 'rgb(153, 27, 27)',
            '900' => 'rgb(127, 29, 29)',
        ],
        'green' => [
            '400' => 'rgb(74, 222, 128)',
            '500' => 'rgb(34, 197, 94)',
            '600' => 'rgb(22, 163, 74)',
            '700' => 'rgb(21, 128, 61)',
            '800' => 'rgb(22, 101, 52)',
            '900' => 'rgb(20, 83, 45)',
        ],
        'purple' => [
            '400' => 'rgb(192, 132, 252)',
            '500' => 'rgb(168, 85, 247)',
            '600' => 'rgb(147, 51, 234)',
            '700' => 'rgb(126, 34, 206)',
            '800' => 'rgb(107, 33, 168)',
            '900' => 'rgb(88, 28, 135)',
        ],
        'indigo' => [
            '400' => 'rgb(129, 140, 248)',
            '500' => 'rgb(99, 102, 241)',
            '600' => 'rgb(79, 70, 229)',
            '700' => 'rgb(67, 56, 202)',
            '800' => 'rgb(55, 48, 163)',
            '900' => 'rgb(49, 46, 129)',
        ],
        'pink' => [
            '400' => 'rgb(244, 114, 182)',
            '500' => 'rgb(236, 72, 153)',
            '600' => 'rgb(219, 39, 119)',
            '700' => 'rgb(190, 24, 93)',
            '800' => 'rgb(157, 23, 77)',
            '900' => 'rgb(131, 24, 67)',
        ],
        'yellow' => [
            '400' => 'rgb(250, 204, 21)',
            '500' => 'rgb(234, 179, 8)',
            '600' => 'rgb(202, 138, 4)',
            '700' => 'rgb(161, 98, 7)',
            '800' => 'rgb(133, 77, 14)',
            '900' => 'rgb(113, 63, 18)',
        ],
    ];

    /**
     * Get seat color styles based on state
     *
     * @param string $state The seat state: 'available', 'selected', or 'booked'
     * @param string|null $color Optional color name. If not provided, will use config value
     * @return array Array with 'backrest', 'base', 'shadow', and 'shadowDark' styles
     */
    public static function getSeatColorStyles(string $state, ?string $color = null): array
    {
        // Get color name from config if not provided
        if ($color === null) {
            $color = config("cine-reserve.seat_colors.{$state}", self::getDefaultColorForState($state));
        }

        // Get color values
        $colorValues = self::getColorValues($color);

        // Return styles based on state
        return match($state) {
            'available' => self::buildAvailableStyles($colorValues),
            'selected' => self::buildSelectedStyles($colorValues),
            'booked' => self::buildBookedStyles($colorValues),
            default => self::buildBookedStyles(self::getColorValues('gray')),
        };
    }

    /**
     * Get legend color styles for display
     *
     * @return array Array with 'available', 'selected', and 'booked' color styles
     */
    public static function getLegendColorStyles(): array
    {
        $availableColor = config('cine-reserve.seat_colors.available', 'emerald');
        $selectedColor = config('cine-reserve.seat_colors.selected', 'amber');
        $bookedColor = config('cine-reserve.seat_colors.booked', 'gray');

        return [
            'available' => self::buildLegendStyles(self::getColorValues($availableColor)),
            'selected' => self::buildLegendStyles(self::getColorValues($selectedColor)),
            'booked' => self::buildLegendStyles(self::getColorValues($bookedColor), true),
        ];
    }

    /**
     * Get color values for a given color name
     *
     * @param string $color The color name (e.g., 'blue', 'emerald', 'amber')
     * @return array Array of RGB color values for different shades
     */
    protected static function getColorValues(string $color): array
    {
        return self::$colorPalette[$color] ?? self::$colorPalette['gray'];
    }

    /**
     * Get default color for a given state
     */
    protected static function getDefaultColorForState(string $state): string
    {
        return match($state) {
            'available' => 'emerald',
            'selected' => 'amber',
            'booked' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Build available seat styles
     */
    protected static function buildAvailableStyles(array $colorValues): array
    {
        return [
            'backrest' => [
                'class' => 'bg-gradient-to-br border-2',
                'style' => "background: linear-gradient(to bottom right, {$colorValues['500']}, {$colorValues['600']}, {$colorValues['700']}); border-color: {$colorValues['800']};",
            ],
            'base' => [
                'class' => 'bg-gradient-to-b',
                'style' => "background: linear-gradient(to bottom, {$colorValues['600']}, {$colorValues['800']});",
            ],
            'shadow' => [
                'class' => '',
                'style' => "background-color: " . self::rgbToRgba($colorValues['900'], 0.4) . ";",
            ],
            'shadowDark' => [
                'class' => '',
                'style' => "background-color: " . self::rgbToRgba($colorValues['900'], 0.6) . ";",
            ],
        ];
    }

    /**
     * Build selected seat styles
     */
    protected static function buildSelectedStyles(array $colorValues): array
    {
        return [
            'backrest' => [
                'class' => 'bg-gradient-to-br border-2',
                'style' => "background: linear-gradient(to bottom right, {$colorValues['400']}, {$colorValues['500']}, {$colorValues['600']}); border-color: {$colorValues['700']};",
            ],
            'base' => [
                'class' => 'bg-gradient-to-b',
                'style' => "background: linear-gradient(to bottom, {$colorValues['500']}, {$colorValues['700']});",
            ],
            'shadow' => [
                'class' => '',
                'style' => "background-color: " . self::rgbToRgba($colorValues['900'], 0.6) . ";",
            ],
            'shadowDark' => [
                'class' => '',
                'style' => "background-color: " . self::rgbToRgba($colorValues['900'], 0.8) . ";",
            ],
        ];
    }

    /**
     * Build booked seat styles
     */
    protected static function buildBookedStyles(array $colorValues): array
    {
        return [
            'backrest' => [
                'class' => 'bg-gradient-to-br border-2',
                'style' => "background: linear-gradient(to bottom right, {$colorValues['400']}, {$colorValues['500']}, {$colorValues['600']}); border-color: {$colorValues['700']};",
            ],
            'base' => [
                'class' => 'bg-gradient-to-b',
                'style' => "background: linear-gradient(to bottom, {$colorValues['500']}, {$colorValues['700']});",
            ],
            'shadow' => [
                'class' => '',
                'style' => "background-color: " . self::rgbToRgba($colorValues['900'], 0.4) . ";",
            ],
            'shadowDark' => [
                'class' => '',
                'style' => "background-color: " . self::rgbToRgba($colorValues['900'], 0.6) . ";",
            ],
        ];
    }

    /**
     * Build legend styles
     */
    protected static function buildLegendStyles(array $colorValues, bool $isBooked = false): array
    {
        if ($isBooked) {
            return [
                'bg' => [
                    'class' => '',
                    'style' => "background-color: {$colorValues['400']};",
                ],
                'bgDark' => [
                    'class' => '',
                    'style' => "background-color: {$colorValues['500']};",
                ],
                'border' => [
                    'class' => 'border',
                    'style' => "border-color: {$colorValues['500']};",
                ],
                'borderDark' => [
                    'class' => '',
                    'style' => "border-color: {$colorValues['400']};",
                ],
            ];
        }

        return [
            'bg' => [
                'class' => '',
                'style' => "background-color: {$colorValues['500']};",
            ],
            'border' => [
                'class' => 'border',
                'style' => "border-color: {$colorValues['600']};",
            ],
        ];
    }

    /**
     * Convert RGB to RGBA
     *
     * @param string $rgb The RGB color string (e.g., "rgb(30, 58, 138)")
     * @param float $opacity The opacity value (0.0 to 1.0)
     * @return string The RGBA color string (e.g., "rgba(30, 58, 138, 0.4)")
     */
    protected static function rgbToRgba(string $rgb, float $opacity): string
    {
        return str_replace('rgb(', 'rgba(', $rgb) . ', ' . $opacity . ')';
    }
}
