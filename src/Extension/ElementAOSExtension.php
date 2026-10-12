<?php

namespace Dynamic\Elemental\AOS\Extension;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Adds per-element scroll-reveal animation controls (AOS, Animate On Scroll)
 * to every Elemental block, and exposes `$AOSAttributes` for holder templates.
 *
 * Settings live on this class, e.g. in project YAML:
 *
 *   Dynamic\Elemental\AOS\Extension\ElementAOSExtension:
 *     enabled: false
 *
 * @property BaseElement|ElementAOSExtension $owner
 * @property ?string $AOS
 * @property bool $DisableAOS
 */
class ElementAOSExtension extends Extension
{
    /**
     * Master switch. False renders no attributes and (see AOSRequirementsExtension) loads no assets.
     *
     * @config
     */
    private static bool $enabled = true;

    /**
     * Animation used when an element has no (or an unrecognised) stored value.
     *
     * @config
     */
    private static string $default_animation = 'fade-up';

    /**
     * Element classes (and subclasses) that never animate themselves, e.g. layout
     * containers whose children animate instead.
     *
     * @config
     */
    private static array $excluded_classes = [
        'WeDevelop\ElementalGrid\Models\ElementRow',
    ];

    /**
     * @config
     */
    private static array $db = [
        'AOS' => 'Varchar(30)',
        'DisableAOS' => 'Boolean',
    ];

    /**
     * @config
     */
    private static array $defaults = [
        'DisableAOS' => false,
        'AOS' => 'fade-up',
    ];

    /**
     * @config
     */
    private static array $AOSOptions = [
        'fade-in' => 'Fade In',
        'fade-up' => 'Fade Up',
        'fade-down' => 'Fade Down',
        'fade-left' => 'Fade Left',
        'fade-right' => 'Fade Right',
        'fade-up-right' => 'Fade Up Right',
        'fade-up-left' => 'Fade Up Left',
        'fade-down-right' => 'Fade Down Right',
        'fade-down-left' => 'Fade Down Left',
        'flip-up' => 'Flip Up',
        'flip-down' => 'Flip Down',
        'flip-left' => 'Flip Left',
        'flip-right' => 'Flip Right',
        'slide-up' => 'Slide Up',
        'slide-down' => 'Slide Down',
        'slide-left' => 'Slide Left',
        'slide-right' => 'Slide Right',
        'zoom-in' => 'Zoom In',
        'zoom-in-up' => 'Zoom In Up',
        'zoom-in-down' => 'Zoom In Down',
        'zoom-in-left' => 'Zoom In Left',
        'zoom-in-right' => 'Zoom In Right',
        'zoom-out' => 'Zoom Out',
        'zoom-out-up' => 'Zoom Out Up',
        'zoom-out-down' => 'Zoom Out Down',
        'zoom-out-left' => 'Zoom Out Left',
        'zoom-out-right' => 'Zoom Out Right',
    ];

    public function updateCMSFields(FieldList $fields): void
    {
        if (!static::isEnabled()) {
            return;
        }

        $fields->addFieldsToTab('Root.Animation', [
            CheckboxField::create('DisableAOS', 'Disable scroll animation'),
            DropdownField::create('AOS', 'Scroll animation', Config::inst()->get(self::class, 'AOSOptions'))
                ->setValue($this->getOwner()->AOS ?: Config::inst()->get(self::class, 'default_animation'))
                ->setDescription(
                    'Played once when the block scrolls into view. '
                    . 'Visitors who prefer reduced motion never see it.'
                ),
        ]);
    }

    public static function isEnabled(): bool
    {
        return (bool) Config::inst()->get(self::class, 'enabled');
    }

    /**
     * The animation to render for this element, or null when it should not animate.
     */
    public function getAOSAnimation(): ?string
    {
        $owner = $this->getOwner();

        if (!static::isEnabled() || $owner->DisableAOS) {
            return null;
        }

        foreach ((array) Config::inst()->get(self::class, 'excluded_classes') as $class) {
            if (is_a($owner, $class)) {
                return null;
            }
        }

        $options = (array) Config::inst()->get(self::class, 'AOSOptions');
        $animation = (string) $owner->AOS;

        return array_key_exists($animation, $options)
            ? $animation
            : (string) Config::inst()->get(self::class, 'default_animation');
    }

    /**
     * Attribute string for the holder's opening tag: ` data-aos="fade-up"`, or empty.
     */
    public function getAOSAttributes(): DBHTMLText
    {
        $animation = $this->getAOSAnimation();
        $html = $animation === null ? '' : ' data-aos="' . Convert::raw2att($animation) . '"';

        return DBHTMLText::create()->setValue($html);
    }
}
