<?php

namespace Dynamic\Elemental\AOS\Extension;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Loads the AOS library and initialiser on front-end page requests.
 *
 * Done at controller level rather than from `getAOSAttributes()` because holder
 * templates are often wrapped in `<% cached %>`, which would skip the call.
 * Opt out (e.g. to bundle AOS in a theme) with:
 *
 *   Dynamic\Elemental\AOS\Extension\AOSRequirementsExtension:
 *     include_assets: false
 */
class AOSRequirementsExtension extends Extension
{
    /**
     * @config
     */
    private static bool $include_assets = true;

    public function onAfterInit(): void
    {
        if (!Config::inst()->get(self::class, 'include_assets') || !ElementAOSExtension::isEnabled()) {
            return;
        }

        Requirements::css('dynamic/silverstripe-elemental-aos:client/dist/aos.css');
        Requirements::javascript('dynamic/silverstripe-elemental-aos:client/dist/aos.js');
        Requirements::javascript('dynamic/silverstripe-elemental-aos:client/dist/aos-init.js');
    }
}
