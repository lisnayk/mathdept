var AvatarTemplateAdmin = {
	changeShowcase: function(){
		var r=confirm("Do you sure want to change this showcase ?");
		if ( r==true ) {
			Joomla.submitbutton('style.apply');
		} 
	}
};