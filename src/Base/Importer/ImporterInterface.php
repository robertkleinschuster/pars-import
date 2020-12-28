<?php


namespace Pars\Import\Base\Importer;


interface ImporterInterface
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function setup(array &$attributes): bool;

    /**
     * @return mixed
     */
    public function run();
}
