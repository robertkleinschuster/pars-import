<?php


namespace Pars\Import\Base\Importer;


use Niceshops\Bean\Type\Base\BeanAwareInterface;
use Niceshops\Bean\Type\Base\BeanAwareTrait;
use Pars\Helper\Validation\ValidationHelperAwareInterface;
use Pars\Helper\Validation\ValidationHelperAwareTrait;
use Pars\Model\Import\ImportBean;

abstract class AbstractImporter implements BeanAwareInterface, ImporterInterface, ValidationHelperAwareInterface
{
    use BeanAwareTrait;
    use ValidationHelperAwareTrait;

    /**
     * AbstractImporter constructor.
     * @param ImportBean $bean
     */
    public function __construct(ImportBean $bean)
    {
        $this->setBean($bean);
    }
}
