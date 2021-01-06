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

    /**
     * @param \DateTime $now
     * @return bool
     */
    public function isAllowed(\DateTime $now): bool
    {
        $day = intval($now->format('w'))+1;
        $hour = intval($now->format('H'));
        $minute = intval($now->format('i'));
        return ($this->getBean()->empty('Import_Day') || $this->getBean()->get('Import_Day') == $day)
            && ($this->getBean()->empty('Import_Hour') || $this->getBean()->get('Import_Hour') == $hour)
            && ($this->getBean()->empty('Import_Minute') || $this->getBean()->get('Import_Minute') == $minute)
            && $this->getBean()->get('Import_Active');

    }

}
