<?php

namespace Dynamic\Elemental\AOS\Tests\Model;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Dev\TestOnly;

class TestElement extends BaseElement implements TestOnly
{
    private static string $table_name = 'AOSTestElement';
}
