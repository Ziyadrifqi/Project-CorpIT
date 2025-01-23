<?php

namespace Myth\Auth\Authentication\Passwords;

use CodeIgniter\Entity\Entity; // Pastikan ini diimpor
use Myth\Auth\Exceptions\AuthException;

/**
 * Class CompositionValidator
 *
 * Checks the general makeup of the password.
 *
 * @see https://pages.nist.gov/800-63-3/sp800-63b.html#sec5
 */
class CompositionValidator extends BaseValidator implements ValidatorInterface
{
    protected $config;

    public function __construct()
    {
        $this->config = config('Auth'); // Pastikan konfigurasi Auth di-load
    }

    public function check(string $password, ?\CodeIgniter\Entity\Entity $user = null): bool
    {
        if (empty($this->config->minimumPasswordLength)) {
            throw AuthException::forUnsetPasswordLength();
        }

        $passed = strlen($password) >= $this->config->minimumPasswordLength;

        if (! $passed) {
            $this->error = lang('Auth.errorPasswordLength', [$this->config->minimumPasswordLength]);
            $this->suggestion = lang('Auth.suggestPasswordLength');
            return false;
        }

        return true;
    }
}
