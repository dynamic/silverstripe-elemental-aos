# Silverstripe Elemental AOS

Per-element scroll-reveal animations for [Silverstripe Elemental](https://github.com/silverstripe/silverstripe-elemental)
blocks, using [AOS](https://michalsnik.github.io/aos/) (Animate On Scroll) 2.3.4.

Every block gets an **Animation** tab in the CMS: pick one of 27 effects, or tick *Disable scroll
animation*. Blocks animate once, as they scroll into view. Visitors who prefer reduced motion see
no animation, and below 992px every effect is normalised to a plain fade-up.

## Installation

```sh
composer require dynamic/silverstripe-elemental-aos
```

Run `dev/build?flush=1`.

## Template

Add `$AOSAttributes` inside the opening tag of your element holder
(`templates/DNADesign/Elemental/Layout/ElementHolder.ss`, and `_Card` / `_VirtualLinked` variants):

```html
<div class="element element-{$ClassName.ShortName.LowerCase}"$AOSAttributes id="$Anchor">
```

For `ElementHolder_VirtualLinked.ss` use `$LinkedElement.AOSAttributes`. The variable renders
nothing when an element is disabled or excluded. Without it in the holder template, nothing
animates, even though the CMS tab and assets are present.

## Configuration

```yaml
Dynamic\Elemental\AOS\Extension\ElementAOSExtension:
  enabled: true                # false: no attributes, no assets, no CMS tab
  default_animation: fade-up   # used for empty or unrecognised stored values
  excluded_classes:            # these classes (and subclasses) never animate themselves
    - WeDevelop\ElementalGrid\Models\ElementRow
  AOSOptions:                  # add or remove effects offered in the CMS
    fade-up: 'Fade Up'

Dynamic\Elemental\AOS\Extension\AOSRequirementsExtension:
  include_assets: true         # false: load AOS yourself (e.g. in your theme bundle)
```

`excluded_classes` and `AOSOptions` are arrays, so YAML merges additively across config files.
To replace them, use `Config::modify()->set()` in `_config.php`.

New and existing elements with no stored value render `default_animation` (and the CMS dropdown
preselects it); set `enabled: false`, or tick *Disable* per element,
to opt out.

## Data

The module adds two columns to `BaseElement`: `AOS` (`Varchar(30)`) and `DisableAOS` (`Boolean`).

## License

BSD-3-Clause. AOS is MIT licensed (`client/dist/AOS-LICENSE.txt`).
