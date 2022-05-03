jQuery(document).ready(function($){
       
	window.sr = ScrollReveal({
		  		scale: 1,
				duration: 750,
				distance: 0,
				easing: 'cubic-bezier(0.42, 0, 0.58, 1)',
			});
			
			sr.reveal('article, .sr');
			
			sr.reveal('.sr-btm', {
				distance: '3em',
				origin: 'bottom',
			});
			
			sr.reveal('.sr-lft', {
				distance: '3em',
				origin: 'left',
			});
			
			sr.reveal('.sr-top', {
				distance: '3em',
				origin: 'top',
			});
			
			sr.reveal('.sr-repeat', {
				distance: '3em',
				origin: 'bottom',
			}, 300);
	
			


    
});