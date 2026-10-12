<?php

namespace Dynamic\Elemental\AOS\Forms;

use Dynamic\Elemental\AOS\Extension\ElementAOSExtension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;

/**
 * Preselects the configured default animation when the element has no stored value,
 * so the CMS shows what the front end actually renders.
 */
class AOSDropdownField extends DropdownField
{
    public function getValue(): mixed
    {
        $value = parent::getValue();

        return $value === null || $value === ''
            ? Config::inst()->get(ElementAOSExtension::class, 'default_animation')
            : $value;
    }
}
