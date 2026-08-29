<?php
declare(strict_types=1);

namespace Monoverse\Core\Blocks;

interface BlockInterface
{
    public function type(): string;

    public function label(): string;

    public function category(): string;

    public function icon(): string;

    public function description(): string;

    public function configurable(): bool;

    public function template(): string;

    public function defaultSettings(): array;

    public function settingsForm(
        array $settings = []
    ): array;

    public function stylesheets(): array;

    public function data(
        array $settings = [],
        array $context = []
    ): array;
}
