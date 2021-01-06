<?php


namespace Pars\Import\Base\Importer;


use Laminas\I18n\Translator\TranslatorAwareInterface;
use Laminas\I18n\Translator\TranslatorAwareTrait;
use Niceshops\Bean\Type\Base\BeanAwareInterface;
use Niceshops\Bean\Type\Base\BeanAwareTrait;
use Niceshops\Bean\Type\Base\BeanInterface;
use Pars\Helper\Validation\ValidationHelperAwareInterface;
use Pars\Helper\Validation\ValidationHelperAwareTrait;

abstract class AbstractImporter implements
    BeanAwareInterface,
    ImporterInterface,
    ValidationHelperAwareInterface,
    TranslatorAwareInterface
{
    use BeanAwareTrait;
    use ValidationHelperAwareTrait;
    use TranslatorAwareTrait;

    /**
     * AbstractImporter constructor.
     * @param BeanInterface $bean
     */
    public function __construct(BeanInterface $bean)
    {
        $this->setBean($bean);
    }

}
