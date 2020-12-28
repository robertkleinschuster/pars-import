<?php


namespace Pars\Import\Base\Authentication\OAuth\Configurable;


use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Niceshops\Bean\Type\Base\AbstractBaseBean;

class ConfigurableResourceOwner extends AbstractBaseBean implements ResourceOwnerInterface
{
    public function getId()
    {
        return $this->get('id');
    }
}
