<?php

return [

	// images collection for media library
	'images_collection' => 'messages',

	// Define conversions for images
	'image_conversions' => [
		'small' => [
            'fit_mode' => \Spatie\Image\Manipulations::FIT_CROP,
            'width' => 200,
            'height' =>200,
        ],
        'medium' => [
            'fit_mode' => \Spatie\Image\Manipulations::FIT_CROP,
            'width' => 360,
            'height' => 360,
        ],
        'large' => [
            'fit_mode' => \Spatie\Image\Manipulations::FIT_MAX,
            'width' => 600,
            'height' => 600,
        ],
	],
];
