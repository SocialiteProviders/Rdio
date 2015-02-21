<?php
namespace SocialiteProviders\Rdio;

use SocialiteProviders\Manager\SocialiteWasCalled;

class RdioExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'rdio',
            __NAMESPACE__.'\Provider',
            __NAMESPACE__.'\Server'
        );
    }
}
