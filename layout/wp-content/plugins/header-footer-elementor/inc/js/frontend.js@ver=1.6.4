( function( $ ) {

	/**
	* Search widget JS
	*/

	var WidgethfeSearchButton = function( $scope, $ ){

		if ( 'undefined' == typeof $scope )
			return;

			var $input = $scope.find( "input.hfe-search-form__input" );
			var $clear = $scope.find( "button#clear" );
			var $clear_with_button = $scope.find( "button#clear-with-button" );
			var $search_button = $scope.find( ".hfe-search-submit" );
			var $toggle_search = $scope.find( ".hfe-search-icon-toggle input" );

		$scope.find( '.hfe-search-icon-toggle' ).on( 'click', function( ){
			$scope.find( ".hfe-search-form__input" ).trigger( 'focus' );						
		});	
		
		$scope.find( ".hfe-search-form__input" ).on( 'focus', function(){
			$scope.find( ".hfe-search-button-wrapper" ).addClass( "hfe-input-focus" );
		});

		$scope.find( ".hfe-search-form__input" ).blur( function() {
			$scope.find( ".hfe-search-button-wrapper" ).removeClass( "hfe-input-focus" );
		});
  		   

		$search_button.on( 'touchstart click', function(){
			$input.submit();
		});

		$toggle_search.css( 'padding-right', $toggle_search.next().outerWidth() + 'px' );

	
		$input.on( 'keyup', function(){
			$clear.style = (this.value.length) ? $clear.css('visibility','visible'): $clear.css('visibility','hidden');
			$clear_with_button.style = (this.value.length) ? $clear_with_button.css('visibility','visible'): $clear_with_button.css('visibility','hidden');
			$clear_with_button.css( 'right', $search_button.outerWidth() + 'px' );
		});

		$clear.on("click",function(){
			this.style = $clear.css('visibility','hidden');
			$input.value = "";
		});
		$clear_with_button.on("click",function(){
			this.style = $clear_with_button.css('visibility','hidden');
			$input.value = "";
		});
		
	};
		/**
	 * Nav Menu handler Function.
	 *
	 */
	var WidgethfeNavMenuHandler = function( $scope, $ ) {

		if ( 'undefined' == typeof $scope )
			return;
		
		var id = $scope.data( 'id' );
		var wrapper = $scope.find('.elementor-widget-hfe-nav-menu ');		
		var layout = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'layout' );
		var flyout_data = $( '.elementor-element-' + id + ' .hfe-flyout-wrapper' ).data( 'flyout-class' );
		var last_item = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'last-item' );
		var last_item_flyout = $( '.elementor-element-' + id + ' .hfe-flyout-wrapper' ).data( 'last-item' );

		var menu_items_links        = $( '.elementor-element-' + id + ' .hfe-nav-menu nav li a' );
		var menu_items_links_flyout = $( '.elementor-element-' + id + ' .hfe-flyout-wrapper li a' );
		if (menu_items_links.length > 0) {
			_handle_current_menu_item_class( menu_items_links );
		}

		if (menu_items_links_flyout.length > 0) {
			_handle_current_menu_item_class( menu_items_links_flyout );
		}

		$( 'div.hfe-has-submenu-container' ).removeClass( 'sub-menu-active' );

		_toggleClick( id );

		_handleSinglePageMenu( id, layout );

		if( 'horizontal' !== layout ){

			_eventClick( id );
		}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches ) {

			_eventClick( id );
		}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {

			_eventClick( id );
		}

		$( '.elementor-element-' + id + ' .hfe-flyout-trigger .hfe-nav-menu-icon' ).off( 'click keyup' ).on( 'click keyup', function() {

			_openMenu( id );
		} );

		$( '.elementor-element-' + id + ' .hfe-flyout-close' ).off( 'click keyup' ).on( 'click keyup', function() {

			_closeMenu( id );
		} );

		$( '.elementor-element-' + id + ' .hfe-flyout-overlay' ).off( 'click' ).on( 'click', function() {

			_closeMenu( id );
		} );	


		$scope.find( '.sub-menu' ).each( function() {

			var parent = $( this ).closest( '.menu-item' );

			$scope.find( parent ).addClass( 'parent-has-child' );
			$scope.find( parent ).removeClass( 'parent-has-no-child' );
		});

		if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
			$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).parent().addClass( 'elementor-button-wrapper' );
			$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).addClass( 'elementor-button' );			
		}

		_borderClass( id );	

		$( window ).on( 'resize', function(){ 

			if( 'horizontal' !== layout ) {

				_eventClick( id );
			}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches ) {

				_eventClick( id );
			}else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {

				_eventClick( id );
			}

			if( 'horizontal' == layout && window.matchMedia( "( min-width: 977px )" ).matches){

				$( '.elementor-element-' + id + ' div.hfe-has-submenu-container' ).next().css( 'position', 'absolute');	
			}

			if( 'expandible' == layout || 'flyout' == layout ){

				_toggleClick( id );
			}else if ( 'vertical' == layout || 'horizontal' == layout ) {
				if( window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))){

					_toggleClick( id );					
				}else if ( window.matchMedia( "( max-width: 1024px )" ).matches && $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') ) {
					
					_toggleClick( id );
				}
			}

			_borderClass( id );	

		});

        // Acessibility functions

  		$scope.find( '.parent-has-child .hfe-has-submenu-container a').attr( 'aria-haspopup', 'true' );
  		$scope.find( '.parent-has-child .hfe-has-submenu-container a').attr( 'aria-expanded', 'false' );

  		$scope.find( '.hfe-nav-menu__toggle').attr( 'aria-haspopup', 'true' );
  		$scope.find( '.hfe-nav-menu__toggle').attr( 'aria-expanded', 'false' );

  		// End of accessibility functions

		$( document ).trigger( 'hfe_nav_menu_init', id );

		$( '.elementor-element-' + id + ' div.hfe-has-submenu-container' ).on( 'keyup', function(e){

			var $this = $( this );

		  	if( $this.parent().hasClass( 'menu-active' ) ) {

		  		$this.parent().removeClass( 'menu-active' );

		  		$this.parent().next().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
		  		$this.parent().prev().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );

		  		$this.parent().next().find( 'div.hfe-has-submenu-container' ).removeClass( 'sub-menu-active' );
		  		$this.parent().prev().find( 'div.hfe-has-submenu-container' ).removeClass( 'sub-menu-active' );
			}else { 

				$this.parent().next().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
		  		$this.parent().prev().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );

		  		$this.parent().next().find( 'div.hfe-has-submenu-container' ).removeClass( 'sub-menu-active' );
		  		$this.parent().prev().find( 'div.hfe-has-submenu-container' ).removeClass( 'sub-menu-active' );

				$this.parent().siblings().find( '.hfe-has-submenu-container a' ).attr( 'aria-expanded', 'false' );

				$this.parent().next().removeClass( 'menu-active' );
		  		$this.parent().prev().removeClass( 'menu-active' );

				event.preventDefault();

				$this.parent().addClass( 'menu-active' );

				if( 'horizontal' !== layout ){
					$this.addClass( 'sub-menu-active' );	
				}
				
				$this.find( 'a' ).attr( 'aria-expanded', 'true' );

				$this.next().css( { 'visibility': 'visible', 'opacity': '1', 'height': 'auto' } );

				if ( 'horizontal' !== layout ) {
						
		  			$this.next().css( 'position', 'relative');			
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
										
  					$this.next().css( 'position', 'relative');		  					
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {
					
  					if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') ) {

  						$this.next().css( 'position', 'relative');	
  					} else if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-none') ) {
  						
  						$this.next().css( 'position', 'absolute');	
  					}
  				}		
			}
		});

		$( '.elementor-element-' + id + ' li.menu-item' ).on( 'keyup', function(e){
			var $this = $( this );

	 		$this.next().find( 'a' ).attr( 'aria-expanded', 'false' );
	 		$this.prev().find( 'a' ).attr( 'aria-expanded', 'false' );
	  		
	  		$this.next().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
	  		$this.prev().find('ul').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
	  		
	  		$this.siblings().removeClass( 'menu-active' );
	  		$this.next().find( 'div.hfe-has-submenu-container' ).removeClass( 'sub-menu-active' );
		  	$this.prev().find( 'div.hfe-has-submenu-container' ).removeClass( 'sub-menu-active' );
		  		
		});
	};

	function _handle_current_menu_item_class( layout_links ) {
		layout_links.each(
			function () {
				var $this = $( this );
				if ($this.is( '[href*="#"]' )) {
					var menu_item_parent = $this.parent();
					menu_item_parent.removeClass( 'current-menu-item current-menu-ancestor' );
					$this.click(
						function () {
							var current_index  = menu_item_parent.index(),
								parent_element = $this.closest( 'ul' );
							parent_element.find( 'li' ).not( ':eq(' + current_index + ')' ).removeClass( 'current-menu-item current-menu-ancestor' );
							menu_item_parent.addClass( 'current-menu-item current-menu-ancestor' );
						}
					)
				}
			}
		);
	}

	function _openMenu( id ) {

		var flyout_content = $( '#hfe-flyout-content-id-' + id );
		var layout = $( '#hfe-flyout-content-id-' + id ).data( 'layout' );
		var layout_type = $( '#hfe-flyout-content-id-' + id ).data( 'flyout-type' );
		var wrap_width = flyout_content.width() + 'px';
		var container = $( '.elementor-element-' + id + ' .hfe-flyout-container .hfe-side.hfe-flyout-' + layout );

		$( '.elementor-element-' + id + ' .hfe-flyout-overlay' ).fadeIn( 100 );

		if( 'left' == layout ) {

			$( 'body' ).css( 'margin-left' , '0' );
			container.css( 'left', '0' );

			if( 'push' == layout_type ) {

				$( 'body' ).addClass( 'hfe-flyout-animating' ).css({ 
					position: 'absolute',
					width: '100%',
					'margin-left' : wrap_width,
					'margin-right' : 'auto'
				});
			}	

			container.addClass( 'hfe-flyout-show' );	
		} else {

			$( 'body' ).css( 'margin-right', '0' );
			container.css( 'right', '0' );

			if( 'push' == layout_type ) {

				$( 'body' ).addClass( 'hfe-flyout-animating' ).css({ 
					position: 'absolute',
					width: '100%',
					'margin-left' : '-' + wrap_width,
					'margin-right' : 'auto',
				});
			}

			container.addClass( 'hfe-flyout-show' );
		}		
	}

	function _closeMenu( id ) {

		var flyout_content = $( '#hfe-flyout-content-id-' + id );
		var layout    = $( '#hfe-flyout-content-id-' + id ).data( 'layout' );
		var wrap_width = flyout_content.width() + 'px';
		var layout_type = $( '#hfe-flyout-content-id-' + id ).data( 'flyout-type' );
		var container = $( '.elementor-element-' + id + ' .hfe-flyout-container .hfe-side.hfe-flyout-' + layout );

		$( '.elementor-element-' + id + ' .hfe-flyout-overlay' ).fadeOut( 100 );	

		if( 'left' == layout ) {

			container.css( 'left', '-' + wrap_width );

			if( 'push' == layout_type ) {

				$( 'body' ).css({ 
					position: '',
					'margin-left' : '',
					'margin-right' : '',
				});

				setTimeout( function() {
					$( 'body' ).removeClass( 'hfe-flyout-animating' ).css({ 
						width: '',
					});
				});
			}	

			container.removeClass( 'hfe-flyout-show' );					
		} else {
			container.css( 'right', '-' + wrap_width );
			
			if( 'push' == layout_type ) {

				$( 'body' ).css({
					position: '',
					'margin-right' : '',
					'margin-left' : '',
				});

				setTimeout( function() {
					$( 'body' ).removeClass( 'hfe-flyout-animating' ).css({ 
						width: '',
					});
				});
			}
			container.removeClass( 'hfe-flyout-show' );
		}	
	}

	function _eventClick( id ){

		var layout = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'layout' );

		$( '.elementor-element-' + id + ' div.hfe-has-submenu-container' ).off( 'click' ).on( 'click', function( event ) {

			var $this = $( this );

			if( $( '.elementor-element-' + id ).hasClass( 'hfe-link-redirect-child' ) ) {

				if( $this.hasClass( 'sub-menu-active' ) ) {

					if( ! $this.next().hasClass( 'sub-menu-open' ) ) {

						$this.find( 'a' ).attr( 'aria-expanded', 'false' );

						if( 'horizontal' !== layout ){

							event.preventDefault();

							$this.next().css( 'position', 'relative' );	
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
							
							event.preventDefault();

							$this.next().css( 'position', 'relative' );	
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches && ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
							
							event.preventDefault();	

							$this.next().css( 'position', 'relative' );	
						}	
					
						$this.removeClass( 'sub-menu-active' );
						$this.nextAll('.sub-menu').removeClass( 'sub-menu-open' );
						$this.nextAll('.sub-menu').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
						$this.nextAll('.sub-menu').css( { 'transition': 'none'} );
					} else{

						$this.find( 'a' ).attr( 'aria-expanded', 'false' );
						
						$this.removeClass( 'sub-menu-active' );
						$this.nextAll('.sub-menu').removeClass( 'sub-menu-open' );
						$this.nextAll('.sub-menu').css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );
						$this.nextAll('.sub-menu').css( { 'transition': 'none'} );

						if ( 'horizontal' !== layout ){

							$this.next().css( 'position', 'relative' );
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
							
							$this.next().css( 'position', 'relative' );	
							
						} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches && ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
							
							$this.next().css( 'position', 'absolute' );				
						}	  								
					}		  											
				} else {

					$this.find( 'a' ).attr( 'aria-expanded', 'true' );
					if ( 'horizontal' !== layout ) {
						
						event.preventDefault();
						$this.next().css( 'position', 'relative');			
					} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
						
						event.preventDefault();
						$this.next().css( 'position', 'relative');		  					
					} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {
						event.preventDefault();

						if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') ) {

							$this.next().css( 'position', 'relative');	
						} else if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-none') ) {
							
							$this.next().css( 'position', 'absolute');	
						}
					}	
							
					$this.addClass( 'sub-menu-active' );
					$this.nextAll('.sub-menu').addClass( 'sub-menu-open' );
					$this.nextAll('.sub-menu').css( { 'visibility': 'visible', 'opacity': '1', 'height': 'auto' } );
					$this.nextAll('.sub-menu').css( { 'transition': '0.3s ease'} );
				}
			}
		});

		$( '.elementor-element-' + id + ' .hfe-menu-toggle' ).off( 'click keyup' ).on( 'click keyup',function( event ) {

			var $this = $( this );

		  	if( $this.parent().parent().hasClass( 'menu-active' ) ) {

	  			event.preventDefault();

				$this.parent().parent().removeClass( 'menu-active' );
				$this.parent().parent().next().css( { 'visibility': 'hidden', 'opacity': '0', 'height': '0' } );

				if ( 'horizontal' !== layout ) {
						
		  			$this.parent().parent().next().css( 'position', 'relative');			
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
										
  					$this.parent().parent().next().css( 'position', 'relative');		  					
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {
					
  					if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') ) {

  						$this.parent().parent().next().css( 'position', 'relative');	
  					} else if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-none') ) {
  						
  						$this.parent().parent().next().css( 'position', 'absolute');	
  					}
  				}
			}else { 

				event.preventDefault();

				$this.parent().parent().addClass( 'menu-active' );

				$this.parent().parent().next().css( { 'visibility': 'visible', 'opacity': '1', 'height': 'auto' } );

				if ( 'horizontal' !== layout ) {
						
		  			$this.parent().parent().next().css( 'position', 'relative');			
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 767px )" ).matches && ($( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile'))) {
										
  					$this.parent().parent().next().css( 'position', 'relative');		  					
				} else if ( 'horizontal' === layout && window.matchMedia( "( max-width: 1024px )" ).matches ) {
					
  					if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') ) {

  						$this.parent().parent().next().css( 'position', 'relative');	
  					} else if ( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-none') ) {
  						
  						$this.parent().parent().next().css( 'position', 'absolute');	
  					}
  				}		
			}
		});
	}

	function _borderClass( id ){

		var last_item = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'last-item' );
		var last_item_flyout = $( '.elementor-element-' + id + ' .hfe-flyout-wrapper' ).data( 'last-item' );
		var layout = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'layout' );

		$( '.elementor-element-' + id + ' nav').removeClass('hfe-dropdown');

		if ( window.matchMedia( "( max-width: 767px )" ).matches ) {

			if( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-mobile') || $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet')){
				
				$( '.elementor-element-' + id + ' nav').addClass('hfe-dropdown');
				if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).parent().removeClass( 'elementor-button-wrapper' );
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).removeClass( 'elementor-button' );	
				}	
			}else{
				
				$( '.elementor-element-' + id + ' nav').removeClass('hfe-dropdown');
				if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).parent().addClass( 'elementor-button-wrapper' );
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).addClass( 'elementor-button' );	
				}
			}
		}else if ( window.matchMedia( "( max-width: 1024px )" ).matches ) {

			if( $( '.elementor-element-' + id ).hasClass('hfe-nav-menu__breakpoint-tablet') ) {
				
				$( '.elementor-element-' + id + ' nav').addClass('hfe-dropdown');
				if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).parent().removeClass( 'elementor-button-wrapper' );
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).removeClass( 'elementor-button' );	
				}
			}else{
				
				$( '.elementor-element-' + id + ' nav').removeClass('hfe-dropdown');
				if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).parent().addClass( 'elementor-button-wrapper' );
					$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).addClass( 'elementor-button' );
				}
			}
		}else {
			var $parent_element = $( '.elementor-element-' + id );
			$parent_element.find( 'nav').removeClass( 'hfe-dropdown' );
			if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
				$parent_element.find( 'li.menu-item:last-child a.hfe-menu-item' ).parent().addClass( 'elementor-button-wrapper' );
				$parent_element.find( 'li.menu-item:last-child a.hfe-menu-item' ).addClass( 'elementor-button' );
			}
		}

		var layout = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'layout' );
		if( 'expandible' == layout ){
			if( ( 'cta' == last_item || 'cta' == last_item_flyout ) && 'expandible' != layout ){
				$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).parent().removeClass( 'elementor-button-wrapper' );
				$( '.elementor-element-' + id + ' li.menu-item:last-child a.hfe-menu-item' ).removeClass( 'elementor-button' );			
			}			
		}
	}

	function _toggleClick( id ){

		if ( $( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).hasClass( 'hfe-active-menu-full-width' ) ){

			$( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).next().css( 'left', '0' );

			var width = $( '.elementor-element-' + id ).closest('.elementor-section').outerWidth();
			var sec_pos = $( '.elementor-element-' + id ).closest('.elementor-section').offset().left - $( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).next().offset().left;
			$( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).next().css( 'width', width + 'px' );
			$( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).next().css( 'left', sec_pos + 'px' );
		}

		$( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).off( 'click keyup' ).on( 'click keyup', function( event ) {

			var $this = $( this );
			var $selector = $this.next();

			if ( $this.hasClass( 'hfe-active-menu' ) ) {

				var layout = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'layout' );
				var full_width = $selector.data( 'full-width' );
				var toggle_icon = $( '.elementor-element-' + id + ' nav' ).data( 'toggle-icon' );

				$( '.elementor-element-' + id).find( '.hfe-nav-menu-icon' ).html( toggle_icon );

				$this.removeClass( 'hfe-active-menu' );
				$this.attr( 'aria-expanded', 'false' );
				
				if ( 'yes' == full_width ){

					$this.removeClass( 'hfe-active-menu-full-width' );
				
					$selector.css( 'width', 'auto' );
					$selector.css( 'left', '0' );
					$selector.css( 'z-index', '0' );
				}				
			} else {

				var layout = $( '.elementor-element-' + id + ' .hfe-nav-menu' ).data( 'layout' );
				var full_width = $selector.data( 'full-width' );
				var close_icon = $( '.elementor-element-' + id + ' nav' ).data( 'close-icon' );

				$( '.elementor-element-' + id).find( '.hfe-nav-menu-icon' ).html( close_icon );
				
				$this.addClass( 'hfe-active-menu' );
				$this.attr( 'aria-expanded', 'true' );

				if ( 'yes' == full_width ){

					$this.addClass( 'hfe-active-menu-full-width' );

					var width = $( '.elementor-element-' + id ).closest('.elementor-section').outerWidth();
					var sec_pos = $( '.elementor-element-' + id ).closest('.elementor-section').offset().left - $selector.offset().left;
				
					$selector.css( 'width', width + 'px' );
					$selector.css( 'left', sec_pos + 'px' );
					$selector.css( 'z-index', '9999' );
				}
			}

			if( $( '.elementor-element-' + id + ' nav' ).hasClass( 'menu-is-active' ) ) {

				$( '.elementor-element-' + id + ' nav' ).removeClass( 'menu-is-active' );
			}else {

				$( '.elementor-element-' + id + ' nav' ).addClass( 'menu-is-active' );
			}				
		} );
	}

	function _handleSinglePageMenu( id, layout ) {
		$( '.elementor-element-' + id + ' ul.hfe-nav-menu li a' ).on(
			'click',
			function () {
				var $this = $( this );
				var link  = $this.attr( 'href' );
				var linkValue = '';
				if ( link.includes( '#' ) ) {
					var index     = link.indexOf( '#' );
					linkValue = link.slice( index + 1 );
				}
				if ( linkValue.length > 0 ) {
					if ( 'expandible' == layout ) {
						$( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).trigger( "click" );
						if ($this.hasClass( 'hfe-sub-menu-item' )) {
							$( '.elementor-element-' + id + ' .hfe-menu-toggle' ).trigger( "click" );
						}
					} else {
						if ( window.matchMedia( '(max-width: 1024px)' ).matches && ( 'horizontal' == layout || 'vertical' == layout ) ) {
							$( '.elementor-element-' + id + ' .hfe-nav-menu__toggle' ).trigger( "click" );
							if ($this.hasClass( 'hfe-sub-menu-item' )) {
								$( '.elementor-element-' + id + ' .hfe-menu-toggle' ).trigger( "click" );
							}
						} else {
							if ($this.hasClass( 'hfe-sub-menu-item' )) {
								_closeMenu( id );
								$( '.elementor-element-' + id + ' .hfe-menu-toggle' ).trigger( "click" );
							}
							_closeMenu( id );
						}
					}
				}
			}
		);
	}

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/navigation-menu.default', WidgethfeNavMenuHandler );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/hfe-search-button.default', WidgethfeSearchButton );
	});
} )( jQuery );
