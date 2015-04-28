# CropBundle

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/it-blaster/crop-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/crop-bundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/it-blaster/crop-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/crop-bundle/build-status/master)
[![License](https://poser.pugx.org/it-blaster/crop-bundle/license.svg)](https://packagist.org/packages/it-blaster/crop-bundle)
[![Total Downloads](https://poser.pugx.org/it-blaster/crop-bundle/downloads)](https://packagist.org/packages/it-blaster/crop-bundle)
[![Latest Unstable Version](https://poser.pugx.org/it-blaster/crop-bundle/v/unstable.svg)](https://packagist.org/packages/it-blaster/crop-bundle)
[![Latest Stable Version](https://poser.pugx.org/it-blaster/crop-bundle/v/stable.svg)](https://packagist.org/packages/it-blaster/crop-bundle)

This bundle is designed to create crops for images using the user-friendly UI in your forms.
It uses the [fengyuanchen/cropper](https://github.com/fengyuanchen/cropper) jquery plugin
and [it-blaster/uploadable-bundle](https://github.com/it-blaster/uploadable-bundle) to handle images uploading.

## Installation

Add it-blaster/crop-bundle to your `composer.json` file and run `composer`

```json
...
"require": {
    "it-blaster/crop-bundle": "dev-master"
}
...
```

Register the bundle in your `AppKernel.php`

```php
...
new Fenrizbes\CropBundle\FenrizbesCropBundle(),
...
```

Configure [it-blaster/uploadable-bundle](https://github.com/it-blaster/uploadable-bundle)

## Usage

**1.** Configure the `CroppableBehavior` for your table in `schema.xml`:
```xml
...
    <behavior name="croppable">
        <parameter name="columns" value="first_image, second_image" />
    </behavior>
...
```
The parameter `columns` must contain one or more columns' names on which you want to apply this behavior
(default value: `image`). If your table contains other file-columns and you use the `UploadableBundle` to handle them,
don't worry: this bundle automatically adds its columns into `UploadableBehavior` configuration.

The `CroppableBehavior` creates two methods for each configured column:
`setCroppable<column_name>` and `getCroppable<column_name>`. (For example, if you configured the behavior as above,
in your base model will be created following methods: `setCroppableFirstImage`, `getCroppableFirstImage`,
`setCroppableSecondImage` and `getCroppableSecondImage`). You have to use this methods if you want to set up
the `croppable` FormType or get a cropped image.

**2.** Include bundle's resources in your page:
- `bundles/fenrizbescrop/lib/js/cropper.min.js` - the cropper plugin
- `bundles/fenrizbescrop/lib/css/cropper.min.css` - cropper's styles
- `bundles/fenrizbescrop/js/croppable.js` - bundle's scripts

Also remember that this bundle doesn't contain the `JQuery` library that's required for the cropper plugin.

**3.** Configure your form:
```php
...
    ->add('CroppableFirstImage', 'croppable', array(
        'width'  => 250,
        'height' => 250
    ))
...
```
This construction adds a crop control for a picture in your form. Required parameters `width` and `height` set up
the minimum size of the crop area. You can read more about the `croppable` FormType's configuration in the relevant section.

After you upload an image, you'll see it with the crop area over it. Now you can select a part of the image and save
crop coordinates.

**4.** The bundle contains a twig filter `crop` that provide you an ability to get a cropped image:
```twig
...
<img src="{{ asset(item.croppableFirstImage | crop) }}" />
...
```
If you configured a few crops for one picture, you can pass an index of the crop you want:
```twig
...
<img src="{{ asset(item.croppableFirstImage | crop(1)) }}" />
...
```

## CroppableFormType configuration

The full set of specific parameters that you can pass to `croppable` field looks as follows:
```php
...
    ->add('CroppableSecondImage', 'croppable', array(
        'label'             => 'Image crops',
        'width'             => 250,
        'height'            => 250,
        'validate'          => false,
        'max_canvas_width'  => 600,
        'max_canvas_height' => 600,
        'instances'         => array(
            array(
                'label'  => 'First crop',
                'width'  => 200,
                'height' => 250
            ),
            array(
                'label'  => 'Second crop',
                'width'  => 100,
                'height' => 100
            )
        )
    ))
...
```

#### `label`

It's not a specific parameter but you need to know that this label display before all the field's controls.

#### `width` and `height`

Determine the default minimum size of the crop area. You can't select a part of image less than minimum.

#### `validate`

This parameter enables or disables validation for the image file. Validation constraints include a check of the
mime-type (only `gif`, `png` and `jpg` formats are allowed) and a check of minimum picture's size (the image can't be
less than the biggest of its crops). Default value: `true`.

#### `max_canvas_width` and `max_canvas_height`

These two values determine the max size of canvas in which will be inserted the original image and the crop control.
The original image will be scaled proportionally. If you want to disable this limitation, set `0` or `false`
to this options. Default values: `640` and `480` pixels.

#### `instances`

`instances` is a very important option. By default there is only one crop for each image. But if you want to do more
different crops for one image, you can configure them in this option. It must be an array of array. Each of them
describes one crop instance and takes three optional parameters:
- `label` - the label for this crop (default `null`)
- `width` adn `height` - the minimum size of the crop area for this crop (by default they inherit values from the base
`width` adn `height`)

## TODO

- Delete previously generated and currently unused crops
