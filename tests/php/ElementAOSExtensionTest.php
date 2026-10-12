<?php

namespace Dynamic\Elemental\AOS\Tests;

use Dynamic\Elemental\AOS\Extension\AOSRequirementsExtension;
use Dynamic\Elemental\AOS\Extension\ElementAOSExtension;
use Dynamic\Elemental\AOS\Tests\Model\TestElement;
use Dynamic\Elemental\AOS\Tests\Model\TestExcludedElement;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\View\Requirements;

class ElementAOSExtensionTest extends SapphireTest
{
    protected static $extra_dataobjects = [
        TestElement::class,
        TestExcludedElement::class,
    ];

    public function testNewElementAnimatesWithDefault(): void
    {
        $this->assertSame(' data-aos="fade-up"', (string) TestElement::create()->AOSAttributes);
    }

    public function testStoredAnimationIsRendered(): void
    {
        $element = TestElement::create(['AOS' => 'zoom-in']);

        $this->assertSame(' data-aos="zoom-in"', (string) $element->AOSAttributes);
    }

    public function testDisabledElementRendersNothing(): void
    {
        $element = TestElement::create(['AOS' => 'zoom-in', 'DisableAOS' => true]);

        $this->assertSame('', (string) $element->AOSAttributes);
    }

    public function testUnknownValueFallsBackToDefault(): void
    {
        $element = TestElement::create(['AOS' => 'not-an-animation']);
        $this->assertSame(' data-aos="fade-up"', (string) $element->AOSAttributes);

        $element = TestElement::create(['AOS' => '']);
        $this->assertSame(' data-aos="fade-up"', (string) $element->AOSAttributes);
    }

    public function testUnknownValueCannotInjectMarkup(): void
    {
        $element = TestElement::create(['AOS' => '" onload="alert(1)']);

        $this->assertSame(' data-aos="fade-up"', (string) $element->AOSAttributes);
    }

    public function testExcludedClassAndSubclassesRenderNothing(): void
    {
        Config::modify()->set(ElementAOSExtension::class, 'excluded_classes', [TestExcludedElement::class]);

        $this->assertSame('', (string) TestExcludedElement::create()->AOSAttributes);
        $this->assertNotSame('', (string) TestElement::create()->AOSAttributes);
    }

    public function testGridRowIsExcludedByDefault(): void
    {
        $this->assertContains(
            'WeDevelop\ElementalGrid\Models\ElementRow',
            Config::inst()->get(ElementAOSExtension::class, 'excluded_classes')
        );
    }

    public function testDisabledModuleRendersNothing(): void
    {
        Config::modify()->set(ElementAOSExtension::class, 'enabled', false);

        $this->assertSame('', (string) TestElement::create()->AOSAttributes);
    }

    public function testConfiguredDefaultAnimation(): void
    {
        Config::modify()->set(ElementAOSExtension::class, 'default_animation', 'fade-in');

        $this->assertSame(' data-aos="fade-in"', (string) TestElement::create(['AOS' => ''])->AOSAttributes);
    }

    public function testFreshElementFollowsConfiguredDefault(): void
    {
        Config::modify()->set(ElementAOSExtension::class, 'default_animation', 'fade-in');

        $this->assertSame(' data-aos="fade-in"', (string) TestElement::create()->AOSAttributes);
    }

    public function testCmsFieldsWhenEnabled(): void
    {
        $fields = TestElement::create()->getCMSFields();

        $this->assertNotNull($fields->fieldByName('Root.Animation'));
        $this->assertSame('fade-up', $fields->dataFieldByName('AOS')->getValue());
        $this->assertNotNull($fields->dataFieldByName('DisableAOS'));
    }

    public function testNoAOSFieldsAreShownWhenDisabled(): void
    {
        Config::modify()->set(ElementAOSExtension::class, 'enabled', false);

        $fields = TestElement::create()->getCMSFields();

        $this->assertNull($fields->fieldByName('Root.Animation'));
        $this->assertNull($fields->dataFieldByName('AOS'));
        $this->assertNull($fields->dataFieldByName('DisableAOS'));
    }

    public function testAssetsLoadOnlyWhenEnabled(): void
    {
        $load = function (): array {
            Requirements::clear();
            (new AOSRequirementsExtension())->onAfterInit();

            return array_keys(Requirements::backend()->getJavascript());
        };

        $this->assertNotEmpty($load());

        Config::modify()->set(ElementAOSExtension::class, 'enabled', false);
        $this->assertEmpty($load());

        Config::modify()->set(ElementAOSExtension::class, 'enabled', true);
        Config::modify()->set(AOSRequirementsExtension::class, 'include_assets', false);
        $this->assertEmpty($load());
    }
}
