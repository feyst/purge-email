<?php
declare(strict_types=1);

namespace App;

final class SymmetricCrypto
{
    private const SEPARATOR = ';';

    final public function encrypt(
        string $message,
        string $password,
        int $opslimit = SODIUM_CRYPTO_PWHASH_OPSLIMIT_SENSITIVE,
        int $memlimit = SODIUM_CRYPTO_PWHASH_MEMLIMIT_SENSITIVE
    ): string
    {
        $config = [
            'version' => 1,
            'key' => [
                'method' => 'sodium_crypto_pwhash',
                'length' => 32,
                'salt' => bin2hex(random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES)),
                'opslimit' => $opslimit,
                'memlimit' => $memlimit,
                'algo' => SODIUM_CRYPTO_PWHASH_ALG_DEFAULT,
            ],
            'encryption' => [
                'method' => 'sodium_crypto_aead_chacha20poly1305_encrypt',
                'nonce' => bin2hex($nonce = random_bytes(8))
            ],
        ];

        $key = $this->key($config['key'], $password);

        $ciphertext = sodium_crypto_aead_chacha20poly1305_encrypt(
            $message,
            $this->additionalData($config),
            $nonce,
            $key,
        );

        return sprintf(
            '%s%s%s',
            base64_encode(json_encode($config)),
            self::SEPARATOR,
            $ciphertext,
        );
    }

    final public function decrypt(string $ciphertext, string $password): string
    {
        $base64End = strpos($ciphertext,self::SEPARATOR);
        $config = json_decode(base64_decode(substr($ciphertext, 0, $base64End)), true);
        $key = $this->key($config['key'], $password);

        return sodium_crypto_aead_chacha20poly1305_decrypt(
            substr($ciphertext, $base64End + strlen(self::SEPARATOR)),
            $this->additionalData($config),
            hex2bin($config['encryption']['nonce']),
            $key,
        );
    }

    final private function key(array $keyConfig, $password): string
    {
        return sodium_crypto_pwhash(
            $keyConfig['length'],
            $password,
            hex2bin($keyConfig['salt']),
            $keyConfig['opslimit'],
            $keyConfig['memlimit'],
            $keyConfig['algo'],
        );
    }

    final private function additionalData(array $config): string
    {
        return json_encode(['config' => $config]);
    }
}