// @codekit-prepend "plugins/fastclick.js";
// @codekit-prepend "plugins/hammer.js";
// @codekit-prepend "plugins/jquery.hammer.js";
// @codekit-prepend "plugins/jquery.nicescroll.min.js";
// @codekit-prepend "plugins/prism.js";
// @codekit-prepend "plugins/underscore-min.js";
// @codekit-prepend "plugins/jquery.twbsPagination.js";
// @codekit-prepend "plugins/bootstrap-calendar/languages/en-US.js";
// @codekit-prepend "plugins/bootstrap-calendar/calendar.js";

/* viewport units polyfill */
(function() {
	var userAgent = window.navigator.userAgent;
	var isOldInternetExplorer = false;
	var isOperaMini = userAgent.indexOf('Opera Mini') > -1;
	var isMobileSafari = /(iPhone|iPod|iPad).+AppleWebKit/i.test(userAgent) && (function() {
		var iOSversion = userAgent.match(/OS (\d)/);
		return iOSversion && iOSversion.length>1 && parseInt(iOSversion[1]) < 8;
	})();
	
	var isBadStockAndroid = (function() {
		var isAndroid = userAgent.indexOf(' Android ') > -1;
		if (!isAndroid) {
			return false;
		}
		var isStockAndroid = userAgent.indexOf('Version/') > -1;
		if (!isStockAndroid) {
			return false;
		}
		
		var versionNumber = parseFloat((userAgent.match('Android ([0-9.]+)') || [])[1]);
		return versionNumber <= 4.4;
	})();
	
	if(isMobileSafari || isBadStockAndroid || isOperaMini) {
		document.documentElement.className += " no-vh";
	}
})();

/* main site functions */
	function getViewportDimensions() {
		var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],x=w.innerWidth||e.clientWidth||g.clientWidth,y=w.innerHeight||e.clientHeight||g.clientHeight;
		return { width:x,height:y }
	}
	
	// debulked onresize handler
	function on_resize(c,t){onresize=function(){clearTimeout(t);t=setTimeout(c,100)};return c};
	
	function initDownArrowScroller() {
		var downArrow = $('.arrow-down');
	    var el = downArrow.attr('data-element');
		downArrow.hammer().on("click", function(ev) {
			var viewport = getViewportDimensions();
			var distance = $(el).offset().top;
			if(viewport.width <= 768) {
				distance -= 65;
			}
			$('html, body').animate({
				scrollTop: distance
			}, 300);
		});
	}
	
	var preloadCarouselImages = function(callback) {
		var $elements = $('.carousel-inner .item').not('.active');
		var toLoad=$elements.length;
		$elements.each(function() {
			var fill = $(this).children('.fill');
			var bgImg = fill.css('background-image');
			bgImg = bgImg.replace('url(','').replace(')','').replace(/["']/g, "");
			$("<img />").attr("src", bgImg).load(function() { //.appendTo('body').hide()
				toLoad--;
				if(toLoad == 0) {callback();}
			});
		});
	};
	
	function scrollToElement(el) {
		$('html, body').animate({
			scrollTop: $(el).offset().top - 20
		}, 500);
	}
	
	function loadTumblrBlock(blockID, page, callbackFunc) {
		var tumblrBlock = '#tumblr-' + blockID;
		$(tumblrBlock).load('assets/ajax/loadTumblrs.php', {blockID:blockID, page: page}, function() {
			$('.pagination a').on('click', function(e) {
				if(!$(this).parent().hasClass('active')) {
					$('.generic-loader').show();
					loadTumblrBlock($(this).attr('data-block-id'), $(this).attr('data-page'), function(){
						scrollToElement(tumblrBlock);
					});
				}
				e.preventDefault();
			});
			//some style tweaks and clean up
			$('.tumblr-feed-block .panel-body img').addClass('img-responsive');
			//empty tags (p and br and b...)
			$('.tumblr-feed-block .panel-body p').each(function() {
				var children = $(this).children();
				children.each(function() {
					if($(this).is(':empty') || $(this).html() == '<br>') {
						$(this).remove();
					}
				});
				if($(this).is(':empty')) {
					$(this).remove();
				}
			});
			//style fixes (padding on bottom / height fixes
			styleFixes();
			
			if(callbackFunc) {
				callbackFunc();
			}
		});
	}
	
	function loadInstagramBlock(blockID, page, callbackFunc) {
		//instagram-feed
		var instaBlock = '#instagram-' + blockID;
		$(instaBlock).load('assets/ajax/loadInstagram.php', {blockID:blockID, page: page}, function() {
			$('.insta-item .thumbnail a').on('click', function() {
				$('#instagram-modal .modal-body').html('<h3><i class="fa fa-spin fa-spinner"></i> Loading...</h3>');
				$('#instagram-modal').modal('show');
				$('#instagram-modal .modal-body').load('assets/ajax/loadInstagramItem.php', {blockID:$(this).attr('data-block-id'), mediaID: $(this).attr('data-insta-id')});
			});
		});
	}
	
	function initSecondaryContent() {
		//load in content and add nice scroll to fix scroll bars on the news elements
			if($('#news-schedule').exists()) {
				var twitterBlockID = $('#news-loader').attr('data-block-id');
				var eventsBlockID = $('#events-loader').attr('data-block-id');
				var twitterTitle = $('#news-loader').attr('data-title');
				
				var eventsTitle = $('#events-loader').attr('data-title');
				var eventsLinksTo = $('#events-loader').attr('data-linksto');
				
				$('#news-loader').load('assets/ajax/loadNewsList.php', {blockID:twitterBlockID, title: twitterTitle, displayStyle:$('#news-loader').attr('data-display')});
				$('#events-loader').load('assets/ajax/loadEventsSchedule.php', {blockID:eventsBlockID, title: eventsTitle, linkTo: eventsLinksTo});		
				
				if(!Modernizr.hiddenscroll) {	
					$('#news-schedule .inner').niceScroll({cursorborder:'none', preservenativescrolling:false, cursorborderradius:'0px', cursorwidth:'8px', autohidemode:'leave', cursoropacitymax:.8});
				} else {
					$('#news-schedule .inner').addClass('native-scroll');
				}
			}
		
		//init other twitter feeds
			$('.twitter-feed-block').each(function() {
				$(this).load('assets/ajax/loadNewsList.php', {blockID:$(this).attr('data-block-id'), title: $(this).attr('title'), displayStyle:$(this).attr('data-display')});
			});
		
		//init instagram blocks
			$('.instagram-feed').each(function() {	
				loadInstagramBlock($(this).attr('data-block-id'), $(this).attr('data-page'), null);
			});
			
		//init tumblr feeds
			$('.tumblr-feed-block').each(function() {
				loadTumblrBlock($(this).attr('data-block-id'), $(this).attr('data-page'), null);
			});
		
		//init other events blocks
			$('.cal-feed-block').each(function() {
				$(this).load('assets/ajax/loadEventsSchedule.php', {blockID:$(this).attr('data-block-id')});
			});
			
		//init calendar blocks
			$('.cal-full-block').each(function() {
				//we need to set some options here, including getting the events json for the given month
				var $thisEl = $(this);
				var blockID = $thisEl.data('block-id');
				var showEventsList = $thisEl.data('events-list');
				var calEl = '#calendar-load-' + blockID;
				var calOptionsEl = '#cal-options-' + blockID;
				var eventsListEl = '#events-list-' + blockID;
				var evHelperWrap = '#ev-list-helper-' + blockID;
				var eventsListMonthEl = '#events-list-month-' + blockID;
				var eventsPaginationID = 'events-pagination-' + blockID; //for creating the ul that holds pagination. w/o the hash!
				var eventsPagination = '#'+eventsPaginationID;
				var eventsPgContain = '#ev-pg-contain-' + blockID;
				var daysPerPage = 100; //for the pagination. set really high to effectively disable pagination and make one scrolling box
				var cacheBust = Date.now();
				var options = {
					events_source: '/assets/ajax/calEventsData.json.php?cb='+cacheBust+'&blockID='+blockID,
					tmpl_path: "/assets/templates/bootstrap-calendar/tmpls/",
					language : 'en-US',
					views: {
			            year: {enable:0},
			            week: {enable:0},
			            day: {enable:0}
			        },
			        modal: '#events-detail-modal',
			        modal_type : "ajax",
			        onAfterViewLoad: function(view) {
				        $(calOptionsEl).find('h3').html(this.getTitle());
						$(calOptionsEl).find('.btn-group button[data-calendar-nav]').removeClass('active');
						if(showEventsList == true) {
							$(eventsListMonthEl).html('<h3 class="event-list-title">'+this.getTitle()+'</h3>');
						}
					},
					onAfterEventsLoad: function(events) {
						if(showEventsList == false) {
							return;
						}
						if(!events) {
							return;
						}
						var eventList = $(eventsListEl);
						eventList.html(''); //reset
						$(evHelperWrap).find('.helper').hide();
						//create a new object that contains the day (ie, '15') as the key and all the events for that day as an array in it
						var monthDays = {};
						$.each(events, function(key, val) {
							if(!monthDays.hasOwnProperty(val.dayNum)){
								monthDays[val.dayNum] = [];
							}
							monthDays[val.dayNum].push(val);
						});
						var dayKeys = _.keys(monthDays); //array of all the month days we have events for. now, we want to paginate it
						var eventPages = [];
						var i,j;
						for (i=0,j=dayKeys.length; i<j; i+=daysPerPage) {
							eventPages.push(dayKeys.slice(i,i+daysPerPage));
						}
						if(eventPages.length) {
							$.each(eventPages, function(pageNum, days) {
								var pageBlock = $('<ul/>').addClass('pageBlock event-output-list list-unstyled').attr('data-page', pageNum+1);
								$.each(days, function(k, dayNum) {
									var newDay = $('<li/>').addClass('dayOfMonth');
									var dayInner = '<span class="day"><span>'+dayNum+'</span></span><ul class="list-unstyled">';
									var events = monthDays[dayNum];
									
									$.each(events, function(key, event) {
										dayInner += '<li><span class="title">';
										if(event.atTime) dayInner += event.atTime + ': ';
										dayInner += event.summary+'</span>';
										if(event.description) dayInner += '<span class="description">' + event.description + '</span>';
										if(event.location) dayInner += '<span class="location"><small><i class="fa fa-location-arrow"></i></small> ' + event.location + '</span>';
										dayInner += '</li>';
									});
									
									dayInner += '</ul>';
									newDay.html(dayInner).appendTo(pageBlock);
								});
								pageBlock.hide().appendTo(eventList);
								if(pageNum==0) {
									pageBlock.show(); //show the first one
									$(eventList).scrollTop(0);
									//$(eventList).animate({scrollTop:0});
									if( (pageBlock.height() + 12) > eventList.height()) {
										$(evHelperWrap).find('.helper').show();
									}
								}
							});
							$(eventList).on('scroll', function() {
								setTimeout(function(){
									$(evHelperWrap).find('.helper').fadeOut('slow');
								}, 2000);
								$(eventList).off('scroll');
							});
							//handle pagination
							if(eventPages.length > 1) {
								if($(eventsPagination).length) $(eventsPagination).remove();
								$('<ul/>', {id:eventsPaginationID}).addClass('event-pages pagination list-unstyled').appendTo($(eventsPgContain)); //brute force //eventsPgContain
								
								$(eventsPagination).twbsPagination({
									totalPages: eventPages.length,
									visiblePages: 7,
									first: '<i class="fa fa-angle-double-left"></i>',
									prev: '<i class="fa fa-chevron-circle-left"></i>',
									next: '<i class="fa fa-chevron-circle-right"></i>',
									last: '<i class="fa fa-angle-double-right"></i>',
									loop: true,
									onPageClick: function (event, page) {
										$thisEl.find('.pageBlock').not('[data-page="'+page+'"]').hide();
										$thisEl.find('.pageBlock[data-page="'+page+'"]').fadeIn('slow');
									}
								});
							}
						}
					}
				}
				
				var calendar = $(calEl).calendar(options);
				$(calOptionsEl).find('.btn-group button[data-calendar-nav]').each(function() {
					var $this = $(this);
					$this.click(function() {
						$(calOptionsEl).find('h3').html('<i class="fa fa-spin fa-spinner"></i> Loading&hellip;');
						setTimeout(function() {
							calendar.navigate($this.data('calendar-nav'));
						}, 100);
						//calendar.navigate($this.data('calendar-nav'));
					});
				});
			});
		
		//init any media blocks
			$('.media-embed').each(function() {
				var block = $(this);
				block.load('assets/ajax/loadMediaItem.php', {blockID:block.attr('data-block-id')}, function(){
					setTimeout(function(){
						block.removeClass('loading');
					}, 500);
				});
			});
		
		//init any visitor-contact-form
			//refresh-captcha
			$('.visitor-contact-form .refresh-captcha').on('click', function() {
				var el = $(this);
				var blockID = el.attr('data-block-id');
				el.children('.fa').addClass('fa-spin');
				$.getJSON('assets/ajax/captchaImg.php', {blockID:blockID}, function(data) {
					$('#captcha-' + blockID).attr("src", data.img);
					el.children('.fa').removeClass('fa-spin');
				});		
			});
			
			$('.visitor-contact-form').submit(function(e) {
				var theForm = $(this);
				var blockID = theForm.attr('data-block-id');
				var messageEl = theForm.find('.visitor-form-results').eq(0);
				messageEl.html('<div class="alert alert-info"><i class="fa fa-spin fa-spinner"></i> Submitting&hellip;</div>').show();		
				$.post('assets/ajax/submitContactForm.php', theForm.serializeArray(), function(data) {
					messageEl.html(data.message);
					if(data.success) {
						theForm.find('.inner-form').slideUp(500, function() {
							messageEl.css({marginTop:'0px'});
						});	
					}
				}, "json");	
				e.preventDefault();
			});
		
		//stop carousels from autoplaying
			$('.content-block .carousel').carousel('pause');
	}
	
	function initMapBoxContactMap() {
		if($('#mapbox').exists()) {		
			var mapSettings = $('#mapbox').data();
			var mapData = [mapSettings.lat, mapSettings.lon];
			var zoom = mapSettings.zoom;
			
			L.mapbox.accessToken = 'pk.eyJ1IjoiZGF2ZWtvYnJlbnNraSIsImEiOiJReWpwRUJnIn0.Hdf7LV4_lVXWzEt9qjWkLw';
			var map = L.mapbox.map('mapbox', null, {
				zoomControl: false,
				tileLayer: {
					detectRetina: true,
					minZoom: 2
				}
			}).setView(mapData, zoom);
			
			map.scrollWheelZoom.disable();
			new L.Control.Zoom({ position: 'bottomright' }).addTo(map);
			
			var mapStyles = new Object();
			mapStyles.outdoors = L.mapbox.tileLayer('davekobrenski.ladefclp'); //default
			mapStyles.satellite = L.mapbox.tileLayer('davekobrenski.lae0b054');	
			//mapStyles.wood = L.mapbox.tileLayer('examples.xqwfusor');
			mapStyles.road = L.mapbox.tileLayer('examples.ra3sdcxr');
			
			mapStyles.outdoors.addTo(map).on('load', function() {
				var loader = $('#mapbox').siblings('.map-loading');
				loader.addClass('done');
				setTimeout(function() {
					loader.addClass('hide');
				}, 500);
			});
			
			setTimeout(function() {
				var marker = L.mapbox.featureLayer({
					type: 'Feature',
					geometry: {
						type: 'Point',
						coordinates: [mapSettings.lon, mapSettings.lat]
					},
					properties: {
						title: mapSettings.maptitle,
						description: mapSettings.address + '<br>' + mapSettings.city,
						'marker-size': 'large',
				        'marker-color': '#AB1840',
				        "marker-symbol": "star"
					}
				}).addTo(map);
			}, 100);
	
			//init map style choosers
			$('.map-chooser a').on('click', function() {
				if(!$(this).hasClass('activemap')) {
					$(this).addClass('activemap');
					$('.map-chooser a').not($(this)).removeClass('activemap');
					var style = $(this).attr('data-map-type');
					var layer = mapStyles[style];		
					if(map.hasLayer(layer)) {
						layer.bringToFront();
					} else {
						layer.addTo(map);
					}
				}
			});
		}
	}
	
	//some style fixes
	function styleFixes() {
		if($('footer').css('position') == 'absolute') {
			var height = $('footer').eq(0).height();
			$('.tertiary .page-content').css({marginBottom: height+'px'});
			
			var minHeight = ($(window).height() - $('.main-nav').outerHeight() - $('.primary-nav').height() - $('footer').height()) + 3;
			$('.tertiary .outer-contain').css({minHeight: minHeight+'px'});
		} else {
			$('.tertiary .page-content').css({marginBottom: '0'});
		}
	}
	
	function alertBarFixes() {
		//has-alert
		if($('body').hasClass('has-alert')) {
			if($('.woahbar .msg').css('display') == 'block') {
				var alertHeight = $('.woahbar .msg').eq(0).outerHeight();
				$('.main-nav.navbar-fixed-top').css({top:alertHeight+'px'});
			} else {
				$('.main-nav.navbar-fixed-top').css('top','');
			}
		}
	}




	//Preloader - when everything is loaded...
	jQuery(window).load(function() {
		//preload the rest of the carousel images
		preloadCarouselImages(function() {
			//$('.carousel-control').show(); //do something here...?
		});
		
		//begin preloader	
	    setTimeout(function() {
			$('#preloader').animate({'opacity' : '0'}, 300, function() {
				jQuery('#preloader').hide();
			});
	    }, 800);
		setTimeout(function() {
			$('.preloader-hide').animate({'opacity' : '1'},300);
			
			//load secondary content, stuff that is below the fold and might otherwise slow up page load
			initSecondaryContent();
			initMapBoxContactMap();
			
			//after page is shown, some eye candy
			setTimeout(function() {
				$('.arrow-down').addClass('animated pulse');
			}, 1600);	
		}, 800);	
	});
	
	
	
	
	//when document is ready
	$(function() {
		//set up error notifying for ajax errors
		$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
			Bugsnag.notify("AjaxError", thrownError + ' ' + settings.url);
		});
		
		//helper fn
		jQuery.fn.exists = function(){return this.length>0;} //use like: if($(selector).exists())....
		
		//get rid of 300ms delay on touch devices
		FastClick.attach(document.body);
		
		//init social buttons in navbar
		$('[data-toggle="tooltip"]').tooltip();
		$('.social-links button').on('click touchstart', function() {
			var url = $(this).attr('data-href');
			var key = $(this).attr('data-key');
			var linkID = 'sociallink-' + key;
			if(key == 'srch') {
				$('<a id="'+linkID+'" />').attr('href', url).text('.').appendTo('body').get(0).click();
			} else {
				$('<a id="'+linkID+'" />').attr('href', url).attr('target', '_blank').text('.').appendTo('body').get(0).click();
			}
			$('#' +linkID).remove();
		});
		
		//init carousel
		if($('.fullscreen-carousel').exists()) {
			//$('.carousel').carousel();
		    $('.carousel').carousel('pause');
		    initDownArrowScroller();
		    
		    //add some swipeage to the carousel for mobile
			var swOptions = {preventDefault:true, threshold:10, velocity:0.1};		
			$('.carousel').hammer(swOptions).on("dragleft swipeleft", function(ev) {
				$('.carousel').carousel('next');
			});
			
			$('.carousel').hammer(swOptions).on("dragright swiperight", function(ev) {
				$('.carousel').carousel('prev');
			});
		}
		
		//init primary nav mega menu stuff	
			//mega menu opens on hover
			var togglerDisplay = $('.primary-nav .navbar-toggle').css('display');
			if(togglerDisplay == 'none') {
				$('.yamm-fw').on({
					mouseenter: function() {
						$(this).addClass('open');
					},
					mouseleave: function() {
						$(this).removeClass('open');
					}
				});
			}
			
			//enable clicking through to parent element href
			$('.yamm-fw .dropdown-toggle').on('click', function() {
				var page = $(this).attr('href');
				document.location.href = page;
			});
			
			//enable/disable on resize
			$(window).resize(function(){
				var togglerDisplay = $('.primary-nav .navbar-toggle').css('display');
				if(togglerDisplay == 'none') {
					$('.yamm-fw').on({
						mouseenter: function() {
							$(this).addClass('open');
						},
						mouseleave: function() {
							$(this).removeClass('open');
						}
					});
				} else {
					$('.yamm-fw').off('mouseenter mouseleave');
				}
			});
		
		//MISC style helpers
			var els = $('.page-content .inner-content .row').children();
			var first = $(els[0]);
			if(first.is('.type-basic')) {
				var content = $(first).children('.block-inner').eq(0);
				var firstEl = $(content).children().eq(0);
				if(firstEl.is('p')) {
					if(firstEl.html() == '') {
						firstEl.remove();
					} else {
						$('<i />').addClass('db db-deco blue').appendTo(firstEl);
						firstEl.addClass('leader');
					}
				}	
			}
		
		//make sure any user inputted rogue images in text blocks are responsive
			$('.content-block.type-basic .block-inner img').addClass('img-responsive');
			
			styleFixes();
			//deal with discrepancies in the absolutely positioned footer height and margins etc
			on_resize(function() {
				if($('footer').css('position') == 'absolute') {
					styleFixes();
				}
			});
			
			alertBarFixes();
			on_resize(function() {
				alertBarFixes();
			});
	});


