<?php


namespace Pars\Import\Authentication\OAuth\Configurable;


use League\OAuth2\Client\Grant\AbstractGrant;

class ConfigurableGrant extends AbstractGrant
{
    protected string $name;

    protected array $requiredRequestParameters;

    /**
     * ConfigurableGrant constructor.
     * @param string $name
     * @param array $requiredRequestParameters
     */
    public function __construct(string $name, array $requiredRequestParameters)
    {
        $this->name = $name;
        $this->requiredRequestParameters = $requiredRequestParameters;
    }


    protected function getName()
    {
        return $this->name;
    }

    protected function getRequiredRequestParameters()
    {
        return $this->requiredRequestParameters;
    }

}
