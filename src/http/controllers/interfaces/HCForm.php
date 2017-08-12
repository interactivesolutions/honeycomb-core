<?php

namespace interactivesolutions\honeycombcore\http\controllers\interfaces;
/**
 * HoneyComb form interface
 *
 * Class HCForm
 */
interface HCForm
{
    public function createForm();

    public function getNewStructure();

    public function getEditStructure(bool $append = false);
}