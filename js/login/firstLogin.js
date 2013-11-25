
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function handleViewProfile(){
	if($('#firstLoginForm').valid() == true){
		window.location = "profile.php";
	}
	else{
		elem = $("#firstLoginForm .modal-body");
		height = elem.height();
		elem.scrollTop(height);
	}
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function handleFindMatch(){
	if($('#firstLoginForm').valid() == true){
		window.location = "find_match.php";
	}
	else{
		elem = $("#firstLoginForm .modal-body");
		height = elem.height();
		elem.scrollTop(height);
	}
}
/*
 * Copyright 2013 Artur Leinweber
 * Date: 2013-01-01
 */
function handleMoreInfoMatch(){
	if($('#firstLoginForm').valid() == true){
		window.location = "help.php#BasePoints";
	}
	else{
		elem = $("#firstLoginForm .modal-body");
		height = elem.height();
		elem.scrollTop(height);
	}
}