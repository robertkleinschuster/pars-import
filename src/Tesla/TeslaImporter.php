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

    public function setup(array &$attributes): bool
    {
        $grant = new ConfigurableGrant('password', ['email', 'password']);
        $accessToken = $this->getProvider()->getAccessToken(
            $grant,
            [
                'email' => $attributes['tesla_username'],
                'password' => $attributes['tesla_password']
            ]
        );
        $attributes['Import_Data'] = $this->getBean()->get('Import_Data');
        $attributes['Import_Data']['access_token'] = $accessToken->jsonSerialize();
        if ($accessToken->getToken()) {
            return true;
        } else {
            $this->getValidationHelper()->addError('tesla_username', 'Error');
            return false;
        }
    }

    public function run()
    {
        $token = new AccessToken((array) $this->getBean()->get('Import_Data')['access_token']);
        $provider = $this->getProvider();
        $request = $provider->getAuthenticatedRequest(
            ConfigurableProvider::METHOD_GET,
            'https://owner-api.teslamotors.com/api/1/vehicles',
            $token
        );
        $request = $request->withAddedHeader('User-Agent', 'PARS');
        $response = $provider->getParsedResponse($request);
        $data = [];
        if (is_array($response['response'])) {
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
        }
        $importData = $this->getBean()->get('Import_Data');
        $importData['data'] = $data;
        $this->getBean()->set('Import_Data', $importData);
    }

}
