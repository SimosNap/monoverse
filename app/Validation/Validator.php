<?php
declare(strict_types=1);

namespace Monoverse\Validation;

/**
 * Valida i dati provenienti da form e richieste HTTP.
 *
 * Responsabilità:
 * - verificare i dati ricevuti;
 * - raccogliere gli errori di validazione;
 * - restituire un esito della validazione.
 *
 * Questa classe NON modifica i dati e NON genera HTML.
 */
class Validator
{
    /**
     * Errori di validazione.
     *
     * @var array<string, string>
     */
    private array $errors = [];

    /**
     * Verifica che un valore sia presente.
     */
    public function required(string $field, mixed $value, string $message): self
    {
        if ($value === null || trim((string) $value) === '') {
            $this->errors[$field] = $message;
        }

        return $this;
    }

    /**
     * Restituisce true se non sono presenti errori.
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Restituisce true se la validazione è fallita.
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Restituisce tutti gli errori.
     *
     * @return array<string, string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Restituisce il primo errore di un campo.
     */
    public function error(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
