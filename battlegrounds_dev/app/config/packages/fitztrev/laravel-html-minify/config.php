<?php 
switch(App::environment()){
	case "dc":
	case "ih":
	return array(

    // Turn on/off minification

		'enabled' => true,

		);
	break;
	default:
	return array(

    // Turn on/off minification

		'enabled' => false,

		);
	break;
}
