<?php

namespace SocialiteProviders\Rdio;

use SocialiteProviders\Manager\SocialiteWasCalled;

class RdioExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('rdio', Provider::class, Server::class);
    }
}
