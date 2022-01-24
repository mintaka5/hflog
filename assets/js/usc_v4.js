/* Preloading images */
(function($) {
	var cache = [];
	$.preLoadImages = function() {
		var args_len = arguments.length;
		for (var i = args_len; i--;) {
			var cacheImage = document.createElement('img');
			cacheImage.src = arguments[i];
			cache.push(cacheImage);
		}
	}
})(jQuery)

/* Clearing & replacing form inputs */
function clear_input_fields(fields){
	$(fields).each(function(){
		var value = $(this).val();
		$(this)
		.focus( function() { if ($(this).val()==value) { $(this).val(""); } })
		.blur( function() { if ($(this).val()=="") { $(this).val(value); } });
	});
}

/* Cookie handling functions */
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
function eraseCookie(name) {
	createCookie(name,"",-1);
}

$(function(){

/*********************
   Feature slideshow
**********************/

	$.getJSON(globals.ajaxurl+'slideshow.php', function(data) {
		
		$('.feature').after('<ul id="slidenav">');
		
		$.each(data.slideshow, function (i, item) {
            $('#slidenav').append('<li><img src="' + this.thumb + '" alt="' + this.title + '" /></li>');
			$.preLoadImages(this.image,this.thumb);
		}); // each
		
		$('#slidenav li:first').addClass('active');
		
		var ready=1;

		$('#slidenav li').each(function(){			
			$(this).click(function(){
				if(ready===1) {
					ready=0;
					$('#slidenav li.active').removeClass('active');
					$(this).addClass('active');

					var n = $(this).index();				
					var original = $('.feature:eq(0)');
					var clone = original.clone(true);
					var slide = data.slideshow[n];
					
					clone.find('img').attr("src",slide.image).attr("alt",slide.title);
					clone.find('.feature-content').attr('style',slide.css);
					clone.find('h2').html(slide.title);
					clone.find('.feature-content p').html(slide.copy);
					// had to explicitly handle the photo credit for Opera otherwise
					// it would not empty the <p> if there was no credit
					if (slide.credit!="") {
						clone.find('.feature-credit').html('Photo: <b>'+slide.credit+'</b>');	
					} else {
						clone.find('.feature-credit').html('');
					}
					clone.insertAfter(original).hide().fadeIn(500, function() {
						original.remove();
						ready=1;
					});
				}
			}); //click
			
			var thumbnail = $(this).children('img');
			$(this).hover(function(){
				thumbnail.hide().fadeIn('fast');                                                                                          
			},function(){
				thumbnail.hide();
			});//hover
			
		}); //each
	}); //getJSON

/*********************
	 Popular links
**********************/

	var poplinks = $("#popular-links");
	var popbutton = $(".popular a");
	poplinks.children(".pagewidth").append('<div class="switch" id="switch">Keep popular links open? <a href="#" class="toggle">No</a></div>');
	poplinks.find("ul").css("margin-left","85px");
	var plswitch = $("#switch a");
	
	// read the popular links cookie
	var cookie = readCookie("poplinks");
	if (cookie==="open") {
		poplinks.insertBefore("#topnav");
		plswitch.addClass("open").text("Yes");
		popbutton.addClass("open");
	} else {
		// hide and move the popular links up to the top
		poplinks.css("display","none").insertBefore("#topnav");
	}
	// handle the Keep Open switch
	plswitch.click(function() {
		var cookieState = readCookie("poplinks");
		if (cookieState==="open") {
			eraseCookie("poplinks");
			$(this).removeClass("open").text("No");
		} else {
			createCookie("poplinks","open",365)
			$(this).addClass("open").text("Yes");
		}
		return false;
	});
	
	// handle the popular links button in the top nav
	popbutton.click(function() {
		if ($(this).hasClass("open")) {
			$(this).removeClass("open");
			poplinks.slideUp(400);
		} else {
			$(this).addClass("open");
			poplinks.slideDown(400).focus();
		}
		return false;
	});
	
/*********************
	  Navigation
**********************/
	$.getJSON(globals.ajaxurl+'menus.php', function(menudata) {
		$("nav.primary>ul>li").each( function() {
			$(this).hover(function(){
				$(this).addClass('active').append('<div class="menu"></div>');
				var n = $(this).index();
				var menuHTML = menudata.menus[n].content;
				$(this).children(".menu").hide().html(menuHTML).delay(200).slideDown('fast');
			},function(){
				$(this).removeClass('active').children(".menu").remove();
			});//hover
		});	//each
	});	//getJSON
	
	// clear the search box onClick
	clear_input_fields('.search-form input[type="text"]');
	
	$('nav.primary a').live('click', function(){
		$('.menu').fadeOut('fast');
		$('nav.primary li.active').removeClass('active');
	});
	
/*********************
	     RCMs
**********************/
	var incrementer = 0;
	var maxMove = 2;
	var slideAmount = 981;
	
	$('p.modules-nav-prev a.prev').hide();
		
	$('p.modules-nav a.next').click(function() {
		slideLeft();
	});
	
	$('p.modules-nav-prev a.prev').click(function() {
		slideRight();
	});
	
	
	function slideRight() {
		if(incrementer > 0) {
			$('#modules').animate({'opacity':'.4'},250).animate({'left':'+=980px'},800).animate({'opacity':'1'},250);
			
			incrementer--;
		}
		
		if(incrementer == 0) {
			$('p.modules-nav-prev a.prev').fadeOut('fast', function() {
				$(this).hide();
			});
		}
		
		if(incrementer == 1) {
			$('p.modules-nav a.next').fadeIn('fast');
		}
	}
	
	function slideLeft() {
		if(incrementer < maxMove) {
			$('#modules').animate({'opacity':'.4'},250).animate({'left':'-=980px'},800).animate({'opacity':'1'},250);
			
			incrementer++;
		}
		
		if(incrementer == maxMove) {
			$('p.modules-nav a.next').fadeOut('fast', function() {
				$(this).hide();
			});
		}
		
		if(incrementer == 1) {
			$('p.modules-nav-prev a.prev').fadeIn('fast');
		}
	}
	
	/*var offset = $('.modules-nav a.next').attr('id').replace('next-', '');
  var limit = parseInt(offset, 10) + 4;
	$.getJSON(globals.ajaxurl+'modules.php', function(data) {
		$.each(data.modules, function (i, item) {
			if(offset >= i && i <= limit) { return true; }
			var footerText = "";
			var playVideo = "";
			switch (this.category) {
				case "news":
					footerText = "Read the full story at <strong>USC News</strong>";
					break;
				case "website":
					footerText = "Visit this <strong>USC website</strong>";
					break;
				case "person":
					footerText = "Read more about this <strong>person</strong>";
					break;
				case "video":
					footerText = "View this <strong>video</strong>";
					playVideo = '<a href="'+this.link+'" class="play"></a>';
					break;
				case "event":
					footerText = "Read more about this <strong>event</strong>";
					break;
				case "survey":
					footerText = "Take the <strong>survey</strong>";
					break;
				case "alert":
					footerText = "Read more about this <strong>alert</strong>";
					break;
				case "leadership":
					footerText = "<strong>Engaged Leadership</strong>";
					break;
				case "other":
					footerText = "Learn more";
					break;
				default:	*/
			//} // switch
			/*if(footerText!="") {
				$('#modules').append('<div class="module '+this.category+'">'+playVideo+'<a href="'+this.link+'"><img src="'+this.image+'"></a> <h2><a href="'+this.link+'">'+this.title+'</a></h2> <p>'+this.copy+'</p><div class="read"><a href="'+this.link+'">'+footerText+'</a></div></div>');
			}
		});*/ // each
	//}); //getJSON
	
	// enable touch-based RCM scolling for devices that support it
	/*if ('ontouchstart' in document) {
		$('.modules-nav-prev,.modules-nav').remove();
		var myScroll = new iScroll('modules-window', {
			snap: true,
			momentum: false,
			hScrollbar: false,
			vScrollbar: false
		});
	} else {*/
		
		// functions for prev/next buttons
		/*$('.modules-nav-prev').hide();
		$('.modules-nav a.next').live('click',function(){
			var offset = $('.modules-nav a.next').attr('id').replace('next-', '');
			var limit = parseInt(offset, 10) + 4;
			$(this).attr('id', 'next-'+limit);
			$('.modules-nav-prev').fadeIn();
			if(offset === '3'){
				if ($.browser.msie && $.browser.version.substr(0,1)<8) { 
					$('.module').fadeOut(400);
					for (i=4;i<=7;i++) {
						$('.module').eq(i).fadeIn(400, function() {
							this.style.removeAttribute('filter');
						});
					}
					$('#modules').css({'height':'397px','overflow':'hidden'});
					$('.modules-nav-prev a').hide().fadeIn(800);
				} else {
					$('#modules').animate({'opacity':'.4'},250).animate({'left':'-980px'},800).animate({'opacity':'1'},250);
					$('p.modules-nav-prev a').hide().fadeIn(800);
				}
			} else if(offset === '7') {
				if ($.browser.msie && $.browser.version.substr(0,1)<8) { 
					$('.module').fadeOut(400);
					for (i=8;i<=11;i++) {
						$('.module').eq(i).fadeIn(400, function() {
							this.style.removeAttribute('filter');
						});
					}
				} else {
					$('#modules').animate({'opacity':'.4'},250).animate({'left':'-1960px'},800).animate({'opacity':'1'},250);
				}
				$(this).fadeOut(800);
			}
			return false;
		});

		$('p.modules-nav-prev a').live('click', function(){
			if ($.browser.msie && $.browser.version.substr(0,1)<8) {
				$('.module').fadeOut(400);
				for (i=0;i<=3;i++) {
					$('.module').eq(i).fadeIn(400, function() {
						this.style.removeAttribute('filter');
					});
				}
			} else {
				$('#modules').animate({'opacity':'.4'},250).animate({'left':'0'},800).animate({'opacity':'1'},250);
			}
			$(this).fadeOut(800);
			$('p.modules-nav a.next').attr('id','next-3').fadeIn(800);
			return false;
		});*/
	//}
}); //End loaded jQuery
