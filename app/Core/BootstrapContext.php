<?php
declare(strict_types=1);

namespace Monoverse\Core;

class BootstrapContext
{
    public function __construct(
        public readonly array $config,
        public readonly array $databaseConfig,
        public readonly array $oauthConfig
    ) {
    }
}