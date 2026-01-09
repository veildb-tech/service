<?php

declare(strict_types=1);

namespace App\Security;

use Exception;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Ecdsa;
use Lcobucci\JWT\Signer\Hmac;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Hmac\Sha384;
use Lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;

final class TokenProcessor
{
    /**
     * @var mixed
     */
    private mixed $signer;

    /**
     * @param RawKeyLoader $keyLoader
     * @param string $cryptoEngine
     * @param string $signatureAlgorithm
     * @param int $ttl
     */
    public function __construct(
        protected RawKeyLoader $keyLoader,
        protected string $cryptoEngine,
        protected string $signatureAlgorithm,
        protected int $ttl
    ) {
        $this->signer = $this->getSignerForAlgorithm($signatureAlgorithm);
    }

    /**
     * Generate secret token
     *
     * @param string $uuid
     * @param string $address
     * @param string $secretKey
     *
     * @return UnencryptedToken
     * @throws Exception
     */
    public function generate(
        string $uuid,
        string $secretKey,
        string $address = '127.0.0.1'
    ): UnencryptedToken {
        $tokenBuilder = $this->getTokenBuilder();

        $now = time();
        $exp = $now + $this->ttl;

        return $tokenBuilder
            ->issuedBy($address)
            ->permittedFor($address)
            ->identifiedBy($uuid)
            ->issuedAt(new \DateTimeImmutable("@{$now}"))
            ->expiresAt(new \DateTimeImmutable("@{$exp}"))
            ->withClaim('uid', $uuid)
            ->withHeader('secret_key', $secretKey)
            ->getToken($this->signer, $this->getSigningKey());
    }

    /**
     * Parse token
     *
     * @param string $token
     *
     * @return Token
     * @throws Exception
     */
    public function parse(string $token): Token
    {
        $parser = new Parser(new JoseEncoder());
        try {
            $token = $parser->parse($token);

            $this->validate($token);

            return $token;
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            throw new Exception('JWT Token is invalid');
        }
    }

    /**
     * Validate token
     *
     * @param Token $token
     *
     * @return void
     * @throws Exception
     */
    public function validate(Token $token): void
    {
        $validator = new Validator();

        try {
            $validator->validate($token, new SignedWith($this->signer, $this->getSigningKey()));
        } catch (\Exception $e) {
            throw new Exception('JWT Token is invalid');
        }
    }

    /**
     * Get token builder
     *
     * @return Builder
     */
    private function getTokenBuilder(): Builder
    {
         return (new Builder(new JoseEncoder(), ChainedFormatter::default()));
    }

    /**
     * Get Signing Key
     *
     * @return InMemory
     * @throws Exception
     */
    private function getSigningKey(): InMemory
    {
        if (class_exists(InMemory::class)) {
            $signingKey = InMemory::plainText(
                $this->keyLoader->loadKey(RawKeyLoader::TYPE_PRIVATE),
                $this->signer instanceof Hmac ? '' : (string) $this->keyLoader->getPassphrase()
            );
        } else {
            $signingKey = InMemory::plainText(random_bytes(32));
        }

        return $signingKey;
    }

    /**
     * @param $signatureAlgorithm
     *
     * @return mixed
     */
    private function getSignerForAlgorithm($signatureAlgorithm)
    {
        $signerMap = [
            'HS256' => Sha256::class,
            'HS384' => Sha384::class,
            'HS512' => Sha512::class,
            'RS256' => \Lcobucci\JWT\Signer\Rsa\Sha256::class,
            'RS384' => \Lcobucci\JWT\Signer\Rsa\Sha384::class,
            'RS512' => \Lcobucci\JWT\Signer\Rsa\Sha512::class,
            'ES256' => \Lcobucci\JWT\Signer\Ecdsa\Sha256::class,
            'ES384' => \Lcobucci\JWT\Signer\Ecdsa\Sha384::class,
            'ES512' => \Lcobucci\JWT\Signer\Ecdsa\Sha512::class,
        ];

        if (!isset($signerMap[$signatureAlgorithm])) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The algorithm "%s" is not supported by %s',
                    $signatureAlgorithm,
                    self::class
                )
            );
        }

        $signerClass = $signerMap[$signatureAlgorithm];

        if (is_subclass_of($signerClass, Ecdsa::class) && method_exists($signerClass, 'create')) {
            return $signerClass::create();
        }

        return new $signerClass();
    }
}
