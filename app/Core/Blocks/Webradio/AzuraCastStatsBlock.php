<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks\Webradio;

use Monoverse\Core\Blocks\BlockInterface;
use Monoverse\Core\Blocks\ValidatesSettingsInterface;
use Monoverse\Services\AzuraCastService;
use Monoverse\Services\Translator;

final class AzuraCastStatsBlock implements
    BlockInterface,
    ValidatesSettingsInterface
{
    public function __construct(
        private AzuraCastService $azuraCast,
        private Translator $translator
    ) {
    }

    public function type(): string
    {
        return 'azuracast_stats';
    }

    public function label(): string
    {
        return 'Statistiche AzuraCast';
    }

    public function category(): string
    {
        return 'webradio';
    }

    public function icon(): string
    {
        return 'fa-chart-simple';
    }

    public function description(): string
    {
        return 'Mostra stato della radio, ascoltatori, bitrate, codec e mount attivi.';
    }

    public function configurable(): bool
    {
        return true;
    }

    public function template(): string
    {
        return 'webradio/azuracast-stats';
    }

    public function defaultSettings(): array
    {
        return [
            'title' => 'Statistiche radio',
            'station_url' => '',
            'show_listeners' => true,
            'show_unique_listeners' => true,
            'show_bitrate' => true,
            'show_codec' => true,
            'show_mounts' => true,
        ];
    }

    public function settingsForm(
        array $settings = []
    ): array {
        return [
            [
                'type' => 'text',
                'name' => 'title',
                'label' => 'Titolo',
                'value' => (string) (
                    $settings['title']
                    ?? 'Statistiche radio'
                ),
            ],
            [
                'type' => 'url',
                'name' => 'station_url',
                'label' => 'URL Now Playing AzuraCast',
                'value' => (string) (
                    $settings['station_url']
                    ?? ''
                ),
                'placeholder' => 'https://radio.example.org/api/nowplaying/1',
                'help' => 'Esempio: https://radio.example.org/api/nowplaying/1',
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_listeners',
                'label' => 'Mostra ascoltatori attuali',
                'checked' => (bool) (
                    $settings['show_listeners']
                    ?? true
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_unique_listeners',
                'label' => 'Mostra ascoltatori unici',
                'checked' => (bool) (
                    $settings['show_unique_listeners']
                    ?? true
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_bitrate',
                'label' => 'Mostra bitrate',
                'checked' => (bool) (
                    $settings['show_bitrate']
                    ?? true
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_codec',
                'label' => 'Mostra codec',
                'checked' => (bool) (
                    $settings['show_codec']
                    ?? true
                ),
            ],
            [
                'type' => 'checkbox',
                'name' => 'show_mounts',
                'label' => 'Mostra mount attivi',
                'checked' => (bool) (
                    $settings['show_mounts']
                    ?? true
                ),
            ],
        ];
    }

    public function stylesheets(): array
    {
        return [
            'widgets/azuracast-stats',
        ];
    }

    public function validateSettings(
        array &$settings
    ): array {
        $stationUrl = rtrim(
            trim(
                (string) (
                    $settings['station_url']
                    ?? ''
                )
            ),
            '/'
        );

        if (
            $stationUrl !== ''
            && (
                filter_var(
                    $stationUrl,
                    FILTER_VALIDATE_URL
                ) === false
                || preg_match(
                    '#/api/nowplaying/[^/]+$#',
                    (string) parse_url(
                        $stationUrl,
                        PHP_URL_PATH
                    )
                ) !== 1
            )
        ) {
            return [
                'station_url' =>
                    $this->translator->translate(
                        'blocks.webradio.azuracast_stats.admin.station_url_error'
                    ),
            ];
        }

        $settings['station_url'] = $stationUrl;

        return [];
    }

    public function data(
        array $settings = [],
        array $context = []
    ): array {
        $stationUrl = rtrim(
            trim(
                (string) (
                    $settings['station_url']
                    ?? ''
                )
            ),
            '/'
        );

        $nowPlaying = $stationUrl !== ''
            ? $this->azuraCast->getNowPlaying($stationUrl)
            : [];

        return [
            'title' => trim(
                (string) (
                    $context['block']['title']
                    ?? ''
                )
            ),
            'station_url' => $stationUrl,
            'now_playing' => $nowPlaying,
            'show_listeners' => filter_var(
                $settings['show_listeners'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
            'show_unique_listeners' => filter_var(
                $settings['show_unique_listeners'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
            'show_bitrate' => filter_var(
                $settings['show_bitrate'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
            'show_codec' => filter_var(
                $settings['show_codec'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
            'show_mounts' => filter_var(
                $settings['show_mounts'] ?? true,
                FILTER_VALIDATE_BOOL
            ),
        ];
    }
}
