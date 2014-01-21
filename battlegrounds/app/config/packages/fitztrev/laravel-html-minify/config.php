<?php 
if (App::environment('local'))
{
	return array(

    // Turn on/off minification

		'enabled' => false,

		);
}

if (App::environment('dc'))
{
	return array(

    // Turn on/off minification

		'enabled' => true,

		);
}

