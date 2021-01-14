<?php


namespace Pars\Import\Tesla;


use League\OAuth2\Client\Token\AccessToken;
use Pars\Import\Base\Authentication\OAuth\Configurable\ConfigurableGrant;
use Pars\Import\Base\Authentication\OAuth\Configurable\ConfigurableProvider;
use Pars\Import\Base\Importer\AbstractImporter;

class TeslaImporter extends AbstractImporter
{

    protected function getProvider()
    {
        return new ConfigurableProvider([
            'clientId' => '81527cff06843c8634fdc09e8ac0abefb46ac849f38fe1e431c2ef2106796384',
            'clientSecret' => 'c7257eb71a564034f9419ee651c7d0e5f7aa6bfbd18bafb5c5c033b093bb2fa3',
            'urlAccessToken' => 'https://owner-api.teslamotors.com/oauth/token',
        ]);
    }

    public function setup(array &$attributes)
    {
        $grant = new ConfigurableGrant('password', ['email', 'password']);
        try {
            $provider = $this->getProvider();
            $accessToken = $provider->getAccessToken(
                $grant,
                [
                    'email' => $attributes['tesla_username'],
                    'password' => $attributes['tesla_password']
                ]
            );
            $attributes['Import_Data'] = $this->getBean()->get('Import_Data');
            if ($accessToken->getToken()) {
                $attributes['Import_Data']['access_token'] = $accessToken->jsonSerialize();
            } else {
                $this->getValidationHelper()->addError('General', json_encode($accessToken->jsonSerialize()));
            }
        } catch (\Exception $exception) {
            if ($this->hasTranslator()) {
                $this->getValidationHelper()->addError(
                    'tesla_username',
                    $this->getTranslator()->translate('login.error.credentials', 'admin')
                );
                $this->getValidationHelper()->addError(
                    'tesla_password',
                    $this->getTranslator()->translate('login.error.credentials', 'admin')
                );
            }
            $this->getValidationHelper()->addError('General', $exception->getMessage());
        }
    }

    public function run()
    {
        try {
            $token = new AccessToken((array)$this->getBean()->get('Import_Data')['access_token']);
            $expires = (new \DateTime())->setTimestamp($token->getExpires());
            $now = new \DateTime();
            $importData = $this->getBean()->get('Import_Data');
            if ($now->diff($expires)->days <= 7) {
                $token = $this->refresh_token($token);
                $importData['access_token'] = $token->jsonSerialize();
            }
            $provider = $this->getProvider();
            $request = $provider->getAuthenticatedRequest(
                ConfigurableProvider::METHOD_GET,
                'https://owner-api.teslamotors.com/api/1/vehicles',
                $token
            );
            $request = $request->withAddedHeader('User-Agent', 'PARS');
            $response = $provider->getParsedResponse($request);
            $data = [];
            if (isset($response['response']) && is_array($response['response'])) {
                foreach ($response['response'] as $item) {
                    $request = $provider->getAuthenticatedRequest(
                        ConfigurableProvider::METHOD_GET,
                        'https://owner-api.teslamotors.com/api/1/vehicles/' . $item['id'] . '/vehicle_data',
                        $token
                    );
                    $request = $request->withAddedHeader('User-Agent', 'PARS');
                    $r = $provider->getParsedResponse($request);
                    $data[$item['id']] = $r['response'];
                }
                $importData['data'] = $data;
            }
            $this->getBean()->set('Import_Data', $importData);
        } catch (\Exception $exception) {
            $this->getValidationHelper()->addError('General', $exception->getMessage());
        }
    }

    /**
     * @param AccessToken $token
     * @return AccessToken|\League\OAuth2\Client\Token\AccessTokenInterface
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    protected function refresh_token(AccessToken $token): AccessToken
    {
        $grant = new ConfigurableGrant('refresh_token', ['refresh_token']);
        return $this->getProvider()->getAccessToken(
            $grant,
            [
                'refresh_token' => $token->getRefreshToken()
            ]
        );
    }

}
