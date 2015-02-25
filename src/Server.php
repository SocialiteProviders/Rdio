<?php
namespace SocialiteProviders\Rdio;

use Laravel\Socialite\One\User;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server as BaseServer;

class Server extends BaseServer
{
    /**
     * {@inheritDoc}
     */
    public function urlTemporaryCredentials()
    {
        return 'http://api.rdio.com/oauth/request_token';
    }

    /**
     * {@inheritDoc}
     */
    public function urlAuthorization()
    {
        return 'https://www.rdio.com/oauth/authorize';
    }

    /**
     * {@inheritDoc}
     */
    public function urlTokenCredentials()
    {
        return 'http://api.rdio.com/oauth/access_token';
    }

    /**
     * {@inheritDoc}
     */
    public function urlUserDetails()
    {
        return 'http://api.rdio.com/1/';
    }

    /**
     * {@inheritDoc}
     */
    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        $data = $data['result'];

        $user         = new User();
        $user->id     = $data['key'];
        $user->name   = $data['firstName'].' '.$data['lastName'];
        $user->avatar = $data['icon500'];
        $user->extra  = array_diff_key($data, array_flip([
            'key', 'icon500', 'firstName', 'lastName',
        ]));

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        return $data['result']['key'];
    }

    /**
     * {@inheritDoc}
     */
    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        return $data['result']['firstName'].' '.$data['result']['lastName'];
    }

    /**
     * {@inheritDoc}
     */
    protected function fetchUserDetails(TokenCredentials $tokenCredentials, $force = true)
    {
        if (!$this->cachedUserDetailsResponse || $force == true) {
            $url = $this->urlUserDetails();

            $client = $this->createHttpClient();

            $header = $this->protocolHeader('POST', $url, $tokenCredentials);
            $authorizationHeader = ['Authorization' => $header];
            $headers = $this->buildHttpClientHeaders($authorizationHeader);

            try {
                $response = $client->post($url, $headers, 'method=currentUser')->send();
            } catch (BadResponseException $e) {
                $response = $e->getResponse();
                $body = $response->getBody();
                $statusCode = $response->getStatusCode();

                throw new \Exception(
                    "Received error [$body] with status code [$statusCode] when retrieving token credentials."
                );
            }

            switch ($this->responseType) {
                case 'json':
                    $this->cachedUserDetailsResponse = $response->json();
                    break;

                case 'xml':
                    $this->cachedUserDetailsResponse = $response->xml();
                    break;

                case 'string':
                    parse_str($response->getBody(), $this->cachedUserDetailsResponse);
                    break;

                default:
                    throw new \InvalidArgumentException("Invalid response type [{$this->responseType}].");
            }
        }

        return $this->cachedUserDetailsResponse;
    }
}
