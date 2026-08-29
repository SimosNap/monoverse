<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Session;
use Monoverse\Validation\Validator;

class InstallerService
{
    public function __construct(
        private Session $session,
        private Validator $validator,
        private Translator $translator
    ) {
    }

    public function saveEdition(string $edition): bool
    {
        $this->validator->required(
            'edition',
            $edition,
            $this->translator->translate(
                'installer.validation.edition_required'
            )
        );

        if ($this->validator->fails()) {
            $this->session->flash(
                'errors',
                $this->validator->errors()
            );

            return false;
        }

        $this->session->set(
            'install.edition',
            $edition
        );

        return true;
    }

    public function getEdition(): ?string
    {
        return $this->session->get(
            'install.edition'
        );
    }

    public function hasEdition(): bool
    {
        return (bool) $this->getEdition();
    }

    public function saveDatabase(array $database): bool
    {
        $this->validator
            ->required(
                'db_host',
                $database['host'] ?? '',
                $this->translator->translate(
                    'installer.validation.database_host_required'
                )
            )
            ->required(
                'db_name',
                $database['name'] ?? '',
                $this->translator->translate(
                    'installer.validation.database_name_required'
                )
            )
            ->required(
                'db_user',
                $database['user'] ?? '',
                $this->translator->translate(
                    'installer.validation.database_user_required'
                )
            );

        if ($this->validator->fails()) {
            $this->session->flash(
                'errors',
                $this->validator->errors()
            );

            $this->session->flash(
                'old',
                $database
            );

            return false;
        }

        $this->session->set(
            'install.database',
            $database
        );

        return true;
    }

    public function getDatabase(): array
    {
        return $this->session->get(
            'install.database',
            []
        );
    }

    public function hasDatabase(): bool
    {
        $database = $this->getDatabase();

        return !empty($database['host'])
            && !empty($database['name'])
            && !empty($database['user']);
    }

    public function saveOAuth(array $oauth): bool
    {
        $clientId = trim(
            (string) ($oauth['client_id'] ?? '')
        );

        $clientSecret = trim(
            (string) ($oauth['client_secret'] ?? '')
        );

       $this->validator
        ->required(
            'oauth_client_id',
            $clientId,
            $this->translator->translate(
                'installer.validation.oauth_client_id_required'
            )
        )
        ->required(
            'oauth_client_secret',
            $clientSecret,
            $this->translator->translate(
                'installer.validation.oauth_client_secret_required'
            )
        );

        if ($this->validator->fails()) {
            $this->session->flash(
                'errors',
                $this->validator->errors()
            );

            $this->session->flash(
                'old',
                [
                    'oauth_client_id' => $clientId,
                ]
            );

            return false;
        }

        $this->session->set(
            'install.oauth',
            [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]
        );

        return true;
    }

    public function getOAuth(): array
    {
        return $this->session->get(
            'install.oauth',
            []
        );
    }

    public function hasOAuth(): bool
    {
        $oauth = $this->getOAuth();

        return !empty($oauth['client_id'])
            && !empty($oauth['client_secret']);
    }

    public function saveAdmin(array $admin): bool
    {
        $username = trim(
            (string) ($admin['username'] ?? '')
        );

        $password = (string) (
            $admin['password'] ?? ''
        );

        $confirm = (string) (
            $admin['password_confirm'] ?? ''
        );

        $this->validator
            ->required(
                'admin_username',
                $username,
                $this->translator->translate(
                    'installer.validation.admin_username_required'
                )
            )
            ->required(
                'admin_password',
                $password,
                $this->translator->translate(
                    'installer.validation.admin_password_required'
                )
            )
            ->required(
                'admin_password_confirm',
                $confirm,
                $this->translator->translate(
                    'installer.validation.admin_password_confirm_required'
                )
            );

        if (
            $password !== ''
            && $confirm !== ''
            && $password !== $confirm
        ) {
            $this->session->flash(
                'errors',
                [
                    'admin_password_confirm' =>
                        $this->translator->translate(
                            'installer.validation.admin_password_mismatch'
                        ),
                ]
            );

            $this->session->flash(
                'old',
                [
                    'admin_username' => $username,
                ]
            );

            return false;
        }

        if ($this->validator->fails()) {
            $this->session->flash(
                'errors',
                $this->validator->errors()
            );

            $this->session->flash(
                'old',
                [
                    'admin_username' => $username,
                ]
            );

            return false;
        }

        $this->session->set(
            'install.admin',
            [
                'username' => $username,
                'password_hash' => password_hash(
                    $password,
                    PASSWORD_DEFAULT
                ),
            ]
        );

        return true;
    }

    public function getAdmin(): array
    {
        return $this->session->get(
            'install.admin',
            []
        );
    }

    public function hasAdmin(): bool
    {
        $admin = $this->getAdmin();

        return !empty($admin['username'])
            && !empty($admin['password_hash']);
    }

    public function canAccessStep(string $step): bool
    {
        return match ($step) {
            'requirements',
            'edition' => true,

            'database' =>
                $this->hasEdition(),

            'oauth' =>
                $this->hasEdition()
                && $this->hasDatabase(),

            'admin' =>
                $this->hasEdition()
                && $this->hasDatabase()
                && $this->hasOAuth(),

            'summary',
            'run' =>
                $this->hasEdition()
                && $this->hasDatabase()
                && $this->hasOAuth()
                && $this->hasAdmin(),

            default => false,
        };
    }

    public function firstIncompleteStep(): string
    {
        if (!$this->hasEdition()) {
            return '/install/edition';
        }

        if (!$this->hasDatabase()) {
            return '/install/database';
        }

        if (!$this->hasOAuth()) {
            return '/install/oauth';
        }

        if (!$this->hasAdmin()) {
            return '/install/admin';
        }

        return '/install/summary';
    }

    public function installationData(): array
    {
        return [
            'edition' => $this->getEdition(),
            'database' => $this->getDatabase(),
            'oauth' => $this->getOAuth(),
            'admin' => $this->getAdmin(),
            'locale' => $this->translator->getLocale(),
        ];
    }
}
