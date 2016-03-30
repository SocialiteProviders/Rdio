<?php

namespace SocialiteProviders\Rdio;

use SocialiteProviders\Manager\OAuth1\AbstractProvider;
use SocialiteProviders\Manager\OAuth1\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'RDIO';

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if (!$this->hasNecessaryVerifier()) {
            throw new \InvalidArgumentException('Invalid request. Missing OAuth verifier.');
        }

        $user = $this->server->getUserDetails($token = $this->getToken());

        return (new User())->setRaw($user->extra)->map([
            'id' => $user->id, 'nickname' => null,
            'name' => $user->name, 'email' => null, 'avatar' => $user->avatar,
        ])->setToken($token->getIdentifier(), $token->getSecret());
    }
}
