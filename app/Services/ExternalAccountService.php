<?php
declare(strict_types=1);

namespace Monoverse\Services;

use Monoverse\Core\Config;
use RuntimeException;

class ExternalAccountService
{
    public function __construct(
        private Config $config,
        private ProfileService $profiles
    ) {
    }

    public function lookupByUid(string $uid): ?array
    {
        $uid = trim($uid);

        if ($uid === '' || $uid === '0') {
            return null;
        }

        $baseUrl = rtrim(
            (string) $this->config->get(
                'oauth.account_uid_lookup_url',
                ''
            ),
            '/'
        );

        if ($baseUrl === '') {
            throw new RuntimeException(
                'Endpoint di verifica account SimosNap non configurato.'
            );
        }

        $data = $this->requestJson(
            $baseUrl . '/' . rawurlencode($uid)
        );

        if (empty($data['success'])) {
            return [
                'exists' => false,
                'uid' => $uid,
                'account' => null,
            ];
        }

        $returnedUid = trim(
            (string) ($data['uid'] ?? '')
        );

        if (
            $returnedUid === ''
            || $returnedUid !== $uid
        ) {
            throw new RuntimeException(
                'UID non coerente nella risposta SimosNap.'
            );
        }

        return [
            'exists' => true,
            'uid' => $returnedUid,
            'account' => trim(
                (string) ($data['account'] ?? '')
            ),
        ];
    }

    public function lookupByUsername(
        string $username
    ): ?array {
        $username = trim($username);

        if ($username === '') {
            return null;
        }

        $baseUrl = rtrim(
            (string) $this->config->get(
                'oauth.account_nick_lookup_url',
                ''
            ),
            '/'
        );

        if ($baseUrl === '') {
            $uidLookupUrl = rtrim(
                (string) $this->config->get(
                    'oauth.account_uid_lookup_url',
                    ''
                ),
                '/'
            );

            $baseUrl = preg_replace(
                '~/lookupaccountuid$~',
                '/lookupnick',
                $uidLookupUrl
            ) ?? '';
        }

        if ($baseUrl === '') {
            throw new RuntimeException(
                'Endpoint di ricerca account SimosNap non configurato.'
            );
        }

        $data = $this->requestJson(
            $baseUrl . '/' . rawurlencode($username)
        );

        if (empty($data['success'])) {
            return [
                'exists' => false,
                'uid' => null,
                'account' => null,
            ];
        }

        $returnedUid = trim(
            (string) ($data['uid'] ?? '')
        );

        if (
            $returnedUid === ''
            || $returnedUid === '0'
        ) {
            throw new RuntimeException(
                'UID mancante nella risposta SimosNap.'
            );
        }

        return [
            'exists' => true,
            'uid' => $returnedUid,
            'account' => trim(
                (string) ($data['account'] ?? '')
            ),
        ];
    }

    public function checkNextDueProfile(): bool
    {
        $profile = $this->profiles
            ->findExternalAccountDueForCheck();

        if ($profile === false) {
            return false;
        }

        $sub = trim(
            (string) ($profile['oauth_sub'] ?? '')
        );

        $uid = trim(
            (string) ($profile['oauth_uid'] ?? '')
        );

        $username = trim(
            (string) ($profile['username'] ?? '')
        );

        if ($sub === '') {
            return false;
        }

        try {
            if ($uid !== '' && $uid !== '0') {
                $result = $this->lookupByUid($uid);
            } else {
                $result = $this->lookupByUsername(
                    $username
                );
            }
        } catch (RuntimeException) {
            return false;
        }

        if ($result === null) {
            return false;
        }

        if (empty($result['exists'])) {
            return $this->profiles
                ->markExternalAccountMissing($sub);
        }

        $returnedUid = trim(
            (string) ($result['uid'] ?? '')
        );

        if (
            ($uid === '' || $uid === '0')
            && $returnedUid !== ''
            && $returnedUid !== '0'
        ) {
            $this->profiles->updateExternalAccountUid(
                $sub,
                $returnedUid
            );
        }

        return $this->profiles
            ->markExternalAccountExisting($sub);
    }

    private function requestJson(string $url): array
    {
        $curl = curl_init($url);

        if ($curl === false) {
            throw new RuntimeException(
                'Impossibile inizializzare la richiesta SimosNap.'
            );
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        $curlError = curl_error($curl);

        $statusCode = (int) curl_getinfo(
            $curl,
            CURLINFO_RESPONSE_CODE
        );

        curl_close($curl);

        if ($response === false) {
            throw new RuntimeException(
                'Errore durante la richiesta SimosNap: '
                . $curlError
            );
        }

        if ($statusCode !== 200) {
            throw new RuntimeException(
                'La richiesta SimosNap ha restituito HTTP '
                . $statusCode
                . '.'
            );
        }

        $data = json_decode(
            $response,
            true
        );

        if (!is_array($data)) {
            throw new RuntimeException(
                'Risposta non valida da SimosNap.'
            );
        }

        return $data;
    }
}
