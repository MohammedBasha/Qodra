 <?php

// no direct access
defined ( '_JEXEC' ) or die ();
class Bt_Shortcodes {
	static $tabs = array ();
	static $tab_count = 0;
	static $images = array ();
	static $image_count = array ();
	static $bar_count = 0;
	static $bars = array ();
	static $pricecol_count = 0;
	static $pricecols = array ();
	public static function heading($atts = null, $content = null) {
		// extend atts
		$atts = shortcode_atts ( array (
				'style' => 'default',
				'heading_text' => 'Heading Text',
				'heading_tag' => 'h3',
				'sub_text' => '',
				'sub_text_size' => 13,
				'align' => 'center',
				'class' => '' 
		), $atts, 'heading' );
		// loadd css for heading
		$asset = bt_query_asset ( 'css', 'content-shortcodes' );
		$html = '<div class="bt-heading bt-heading-style-%s bt-heading-align-%s %s">';
		$html .= '<%s>%s</%s>';
		$html .= '<span class="bt-heading-sub-text" style="font-size: %spx">%s</span>';
		$html .= '</div>';
		$html = sprintf($html, $atts['style'], $atts['align'], $atts['class'], $atts['heading_tag'], $atts['heading_text'], $atts['heading_tag'], $atts['sub_text_size'], $atts['sub_text']);
		
		return $asset . $html;
		
	}
	public static function tabs($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'active' => 1,
				'style' => 'default',
				'number' => 3,
				'fitwidth' => 'no',
				'class' => '' 
		), $atts, 'tabs' );
	
		do_shortcode ( $content );
		$return = '';
		$tabs = $panes = array ();
		if (is_array ( self::$tabs )) {
			if (self::$tab_count < $atts ['active'])
				$atts ['active'] = self::$tab_count;
				
			if(($atts['style'] == 'default' || $atts['style'] == 'bottom') && $atts['fitwidth'] == 'yes'){	
				$spanWidth = 100 / self::$tab_count;
				$spanWidth = 'width: ' . $spanWidth . '%;';
			}else{
				$spanWidth = '';
			}
			foreach ( self::$tabs as $tab ) {
				$icon = '';
				if($tab['icon']){
					if (strpos($tab ['icon'], 'icon:') !== false) {
						$tab['icon'] = str_replace('icon:', '', $tab['icon']);
						$icon = '<i class="fa fa-' . $tab ['icon'] . '"></i>';
					}else{
						$icon = '<img style="width: 1em;" alt="" src="' . $tab['icon'] . '"/>';
					}
				}
				
				$tabs [] = '<span class="' . bt_ecssc ( $tab ) . '" style="'. $spanWidth .'">' . $icon . $tab ['title'] . '</span>';
				$panes [] = '<div class="bt-tabs-pane bt-clearfix">' . $tab ['content'] . '</div>';
			}
			$styleClass = 'bt-tabs-' . $atts['style'];
			if($spanWidth){
				$styleClass .= ' bt-tabs-fitwidth ';
			}
			$return = '<div class="bt-tabs ' . $styleClass . bt_ecssc ( $atts ) . '" data-active="' . ( string ) $atts ['active'] . '">';
			if($atts['style'] == 'bottom'|| $atts['style'] == 'vertical-right'){
				$return .= '<div class="bt-tabs-panes">' . implode ( "\n", $panes ) . '</div><div class="bt-tabs-nav">' . implode ( '', $tabs ) . '</div>';	
			}else{
				$return .= '<div class="bt-tabs-nav">' . implode ( '', $tabs ) . '</div><div class="bt-tabs-panes">' . implode ( "\n", $panes ) . '</div>';	
			}
			$return .= '</div>';
			
		}
		// Reset tabs
		self::$tabs = array ();
		self::$tab_count = 0;
		
		// load css
		$css_asset = bt_query_asset ( 'css', 'box-shortcodes' );
		// load jquery 1.10
		$jquery_asset = bt_query_asset ( 'js', 'jquery' );
		
		$mainframe = JFactory::getApplication ();
		if ($mainframe->isAdmin ()) {
			// load js for tabs
			$js_asset = bt_query_asset ( 'js', 'shortcodes' );
		} else {
			// load js for tabs
			$js_asset = bt_query_asset ( 'js', 'shortcode-frontend' );
		}
		return $css_asset . $jquery_asset . $js_asset . $return;
	}
	public static function tab($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'title' => 'Tab title',
				'class' => '',
				'icon'	=> '' 
		), $atts, 'tab' );
		$x = self::$tab_count;
		self::$tabs [$x] = array (
				'title' => $atts ['title'],
				'content' => do_shortcode ( $content ),
				'class' => $atts ['class'],
				'icon' => $atts['icon'] 
		);
		self::$tab_count ++;
	}
	public static function slider($atts = null, $content = null) {
		$mainframe = JFactory::getApplication ();
		$isAdmin = $mainframe->isAdmin ();
		$return = '';
		$atts = shortcode_atts ( array (
				'uid' => '',
				'source' => 'none',
				'limit' => 20,
				'gallery' => null,
				'target' => '_self',
				'width' => 0,
				'crop' => 'no',
				'thumbnail_width' => 800,
				'thumbnail_height' => 600,
				'quality' => 80,
				'crop_center' => true,
				'show_title' => 'yes',
				'centered' => 'yes',
				'arrows' => 'yes',
				'pagination' => 'yes',
				'autoplay' => 3000,
				'speed' => 600,
				'class' => '' 
		), $atts, 'slider' );
		
		do_shortcode ( $content );
		$slides = self::$images;
		
		// set up data attributes
		$data = '';


		if ($atts ['arrows'] == 'yes') {
			$data .= ' data-navigation="true"';
		}
		
		if ($atts ['pagination'] == 'none') {
			$data .= ' data-pagination="false"';
		} else if ($atts ['pagination'] == 'number') {
			$data .= ' data-pagination="number"';
		}
		
		$data .= ' data-autoplay="' . $atts ['autoplay'] . '"';
		$data .= ' data-slide-speed="' . $atts ['speed'] . '"';
		
		// Loop slides
		if (count ( $slides )) {
			// Prepare unique ID
			$id = 'bt-slider-' . $atts['uid'];
			// Links target
			if ($isAdmin) {
				$atts ['target'] = 'popup';
			}
			if ($atts ['target'] === 'blank') {
				$target = ' target="_blank"';
			} elseif ($atts ['target'] === 'popup') {
				$target = "onclick='window.open(this.href,\"Popup\",\"height=600,width=800,resizable=1,scrollbars=1\"); return false;'";
			} else {
				$target = '';
			}

			// Prepare width and height
			$size = ($atts ['width'] == '0') ? 'width:100%' : 'width:' . intval ( $atts ['width'] ) . 'px;';
			
			// Create slides
			$return = '<div id="' . $id . '" class="btsc-slider ' . $atts['class'] . '" style="' . $size . ($atts ['centered'] ? ' margin: auto;' : '') . '" ' . $data . '>';
				
			$count_slide = 0;
			for($i = 0; $i < count ( $slides ); $i ++) {
				// set url image
				$url = parse_url ( $slides [$i] ['src'] );
				if (! isset ( $url ['host'] )) {
					$slides [$i] ['src'] = JURI::root () . $slides [$i] ['src'];
				}
			
				if ($isAdmin) {
					// set link go to article
					$slides [$i] ['link'] = str_replace ( 'administrator/', '', $slides [$i] ['link'] );
				} else {
					// process to create image
					if($atts['crop'] == 'yes'){
						
						$slides [$i] ['src'] = Bt_shortcodeHelper::createThumb ($id, $slides [$i] ['src'], $atts ['thumbnail_width'], $atts ['thumbnail_height'], $atts ['crop_center'], $atts ['quality'] );
					}
				}
			
				// open item
				$return .= '<div class="bt-carousel-item" data-src="' . $slides [$i] ['src'] . '" data-title="' . $slides [$i] ['title'] . '" data-desc="" data-link="' . $slides [$i] ['link'] . '">';
				$return .= '<div class="item-image">';
				if ($slides [$i] ['link']) {
					$return .= '<a href="' . $slides [$i] ['link'] . '" ' . $target . '><img src="' . $slides [$i] ['src'] . '" alt=""></a>';
				} else {
					$return .= '<img src="' . $slides [$i] ['src'] . '" alt="">';
				}
				if($slides[$i]['title'] && $atts['show_title'] == 'yes'){
					$return .= '<h4 class="image-title">';
					if($slides[$i]['link']){
						$return .= '<a href="' . $slides [$i] ['link'] . '" ' . $target . '>' . $slides[$i]['title'] . '</a>';
					}else{
						$return .= $slides[$i]['title'];	
					}
					$return .='</h4>';
				}
				// close item
				$return .= '</div></div>';
			}
			// Close slides
			$return .= '</div>';
			
			
			
			
			// reset slider
			self::$images = array ();
			//self::$image_count ['slider'] = 0;
			$style = bt_query_asset('css', 'galleries-shortcodes');
			$style .= bt_query_asset ( 'css', 'owl.carousel' );
			$jquery = bt_query_asset ( 'js', 'jquery' );
			$owlCarousel = bt_query_asset ( 'js', 'owl.carousel.min' );
			$galleriesJs = bt_query_asset ( 'js', 'galleries-shortcodes' );
			$return .= $style . $jquery . $owlCarousel . $galleriesJs;

		} 		// Slides not found
		else
			$return = '<p class="btsc-error">Slider: images not found.</p>';
		
		return $return;
	}
	public static function image($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'src' => '',
				'title' => 'Image title',
				'link' => '' 
		), $atts, 'image' );
		
		self::$images [] = array (
				'src' => $atts ['src'],
				'title' => $atts ['title'],
				'link' => $atts ['link'] 
		);
		
		return;
	}
	
	
	public static function lightbox($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'src' => false,
				'type' => 'iframe',
				'class' => '' 
		), $atts, 'lightbox' );
		if (! $atts ['src'])
			return '<p class="bt-error">Lightbox: ' . JText::_ ( 'SOURCE_INCORECT' ) . '</p>';
		bt_query_asset ( 'css', 'magnific-popup' );
		bt_query_asset ( 'js', 'jquery' );
		bt_query_asset ( 'js', 'jquery.magnific-popup.min' );
		bt_query_asset ( 'js', 'galleries-shortcodes' );
		return '<span class="bt-lightbox ' . bt_ecssc ( $atts ) . '" data-mfp-src="' . $atts ['src'] . '" data-mfp-type="' . $atts ['type'] . '">' . do_shortcode ( $content ) . '</span>';
	}
	public static function button($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'url' => JURI::root (),
				'link' => null,
				'target' => '_self',
				'style' => 'default',
				'background' => '#2D89EF',
				'color' => '#FFFFFF',
				'border' => 'none',
				'size' => 3,
				'wide' => 'no',
				'center' => 'no',
				'radius' => 'auto',
				'icon' => false,
				'icon_color' => '#FFFFFF',
				'box_shadow' => 'none',
				'text_shadow' => 'none',
				'desc' => '',
				'onclick' => '',
				'class' => '' 
		), $atts, 'button' );
		
		if (! $atts ['link'])
			$atts ['link'] = $atts ['url'];
			// div wrap button to align center
		$before = $after = '';
		if ($atts ['center'] === 'yes') {
			$before = '<div class="btsc-button-center">';
			$after = '</div>';
		}
		
		// Style for a tag : color, background-color, border, border-radius
		$a_styles = array (
				'color' => $atts ['color'],
				'background-color' => $atts ['background'],
				'font-size' => $atts ['size'] . 'px',
				'border' => $atts ['border'],
				'border-radius' => $atts ['radius'] . 'px',
				'-moz-border-radius' => $atts ['radius'] . 'px',
				'-webkit-border-radius' => $atts ['radius'] . 'px',
				'text-shadow' => $atts ['text_shadow'],
				'-moz-text-shadow' => $atts ['text_shadow'],
				'-webkit-text-shadow' => $atts ['text_shadow'],
				'box-shadow' => $atts ['text_shadow'],
				'-moz-box-shadow' => $atts ['text_shadow'],
				'-webkit-box-shadow' => $atts ['text_shadow']
		);
		$a_css = array ();
		foreach ( $a_styles as $rule => $value ) {
			$a_css [] = $rule . ':' . $value;
		}
		
		// Style span tag: color , shadow, padding, font size, line-height, border-color, border-radius,
		
		// Prepare button class
		$class = array (
				'btsc-button' 
		);
		// button style
		$class [] = 'btsc-button-style-' . $atts ['style'];
		// addition class
		$class [] = $atts ['class'];
		// wide button
		if ($atts ['wide'] === 'yes')
			$class [] = 'btsc-button-wide';
			// Prepare icon
		$icon = '';
		if (strpos($atts ['icon'], 'icon:') !== false) {
			$atts['icon'] = str_replace('icon:', '', $atts['icon']);
			$icon = '<i class="fa fa-' . $atts ['icon'] . '"></i>';
		}else{
			$icon = '<img width="' . $atts['size'] . 'px;" alt="" src="' . $atts['icon'] . '"/>';
		}
		
		
		
		// Prepare content
		switch ($atts ['style']) {
			case 'btn1' :
			case 'btn4' :	
				$content = '<span class="btsc-button-icon">' . $icon . '</span> <span class="btsc-button-text">' . $content .'</span>';
				break;
			default :
				$content = '<span  class="btsc-button-text">' . $content .'</span> <span class="btsc-button-icon">' . $icon . '</span>';
				break;
		}
		
		// prepare target
		$targetAction = '';
		if ($atts ['target'] === 'popup') {
			$atts ['target'] = '';
			$targetAction = "window.open(this.href,'Popup','height=600,width=800,resizable=1,scrollbars=1'); return false;";
		}
		// Prepare onClick function
		if ($atts ['onclick'] || $targetAction) {
			$atts ['onclick'] = $atts ['onclick'] . ' ' . $targetAction . '"';
		}
		
		// load css
		$css = bt_query_asset ( 'css', 'content-shortcodes' );
		$awesomeFont = bt_query_asset ( 'css', 'font-awesome.min' );
		return $css . $before . '<a href="' . $atts ['link'] . '" class="' . implode ( $class, ' ' ) . '" onClick="' . $atts ['onclick'] . '" style="' . implode ( $a_css, ';' ) . '" target="' . $atts ['target'] . '">' . do_shortcode ( $content ) . '</a>' . $after;
	}
	public static function iconbox($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'style' => 'icon-inleft-df',
				'title' => 'Box title',
				'title_color' => '#259b9a',
				'icon' => '',
				
				'icon_size' => 14,
				'font_size' => 12,
				'text_color' => '#c8c8c8',
				'readmore_link' => '',
				'class' => ''
		), $atts, 'iconbox' );
		
		// prepare class
		$class = array (
				$atts['class'],
				'btsc-iconbox-style-' . $atts ['style'] 
		);
		
		if ($atts ['style'] === 'icon-ontop-inside' || $atts ['style'] === 'icon-ontop-outside') {
			$icon_ontop = true;
			$class [] = 'btsc-iconbox-style-icon-ontop';
			$content_margin = '';
		} else {
			$icon_ontop = false;
			$class [] = 'btsc-iconbox-style-icon-inleft';
			$content_margin = ((($atts ['icon_size']) * 2 + 10)) . 'px;';
		}
		$return = '<div class="btsc-iconbox ' . implode ( ' ', $class ) . '" >';
		// prepare icon
		
		if ($atts ['style'] === 'icon-inleft-df') {
			$color_icon = $atts ['title_color'];
			$icon_bg_color_style = '';
			$margin_top = ($atts ['font_size'] / 2) . 'px';
		} else {
			$color_icon = '#fff';
			$icon_bg_color_style = 'background-color:' . $atts ['title_color'];
			$margin_top = ($atts ['font_size']) . 'px';
		}
		$icon_style = array (
				'font-size' => $atts ['icon_size'] . 'px',
				'color' => $color_icon,
				'line-height' => (($atts ['icon_size']) * 2) . 'px',
				'width' => (($atts ['icon_size']) * 2) . 'px',
				'margin-top' => ($icon_ontop ? '0' : $margin_top) 
		);
		
		$icon_css = array (
				$icon_bg_color_style 
		);
		foreach ( $icon_style as $rule => $val ) {
			$icon_css [] = $rule . ':' . $val;
		}
		$wrap_icon_style = ($atts ['style'] === 'icon-ontop-outside' ? 'margin-bottom:' . (- $atts ['icon_size'] * 2 / 3) . 'px' : '0px');
		
		if (strpos ( $atts ['icon'], 'icon:' ) !== false) {
			$icon = '<i class="fa fa-' . trim ( str_replace ( 'icon:', '', $atts ['icon'] ) ) . ' icon" style="' . implode ( ';', $icon_css ) . '"></i>';
		} else {
			$icon = '<img class="icon" alt="" src="' . JURI::root () . '/' . $atts ['icon'] . '" style="' . implode ( ';', $icon_css ) . '"/>';
		}
		
		$iconbox = '<div class="btsc-iconbox-icon" style="' . $wrap_icon_style . '">' . $icon .' </div>';
		// prepare title
		$title_style = array (
				'font-size' => ($atts ['font_size'] + 6) . 'px',
				'color' => $atts ['title_color'] 
		);
		$title_css = array ();
		foreach ( $title_style as $rule => $val ) {
			$title_css [] = $rule . ':' . $val;
		}
		$title = '<span class="btsc-iconbox-title" style="' . implode ( ';', $title_css ) . '">' . $atts ['title'] . '</span>';
		// prepare content
		$desc_text_style = array (
				'font-size' => $atts ['font_size'] . 'px',
				'line-height' => ($atts ['font_size'] * 2) . 'px',
				'color' => $atts ['text_color'] 
		);
		$desc_text_css = array ();
		foreach ( $desc_text_style as $rule => $val ) {
			$desc_text_css [] = $rule . ':' . $val;
		}
		$desc_text = '<span class="btsc-iconbox-description" style="' . implode ( ';', $desc_text_css ) . '">' . do_shortcode ( $content ) . '</span>';
		// prepare readmore link
		$link_style = array (
				'color:' . ($atts ['style'] === 'icon-ontop-outside' ? '#FFF' : $atts ['title_color']),
				'font-size:' . ($atts ['font_size'] + 2) . 'px',
				'line-height:' . (($atts ['font_size'] + 2) * 2) . 'px' 
		);
		$link = '';
		$bgr_link = ($atts ['style'] === 'icon-ontop-outside' ? 'background-color:' . $atts ['title_color'] : '');
		if ($atts ['readmore_link']) {
			$link = '<span class="btsc-iconbox-link" style="' . $bgr_link . '"><a href="' . $atts ['readmore_link'] . '" style="' . implode ( ';', $link_style ) . '">' . JText::_ ( 'Read more' ) . '</a></span>';
		}
		
		// prepare content style
		$content_style = array (
				'margin-left:' . $content_margin,
				'padding:' . ($atts ['style'] === 'icon-ontop-outside' ? (10 + round ( $atts ['icon_size'] * 2 / 3 )) . 'px 20px 20px 20px' : '0') 
		);
		$return .= $iconbox . '<div class="btsc-iconbox-content-text" style="' . implode ( ';', $content_style ) . '" >' . $title . $desc_text . $link . '</div></div>';
		// load style
		$font = bt_query_asset ( 'css', 'font-awesome.min' );
		$css = bt_query_asset ( 'css', 'content-shortcodes' );
		
		return $css . $return;
	}
	public static function messagebox($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'width' => '400',
				'font_size' => 12,
				'icon' => 'info',
				'background_color' => '',
				'text_color' => '#000',
				'border' => 'none',
				'class' => '' 
		), $atts, 'messagebox' );
		
		// Prepare icon
		$icon_style = array (
				'font-size:' . ($atts ['font_size'] + 4) . 'px',
				'line-height:' . ($atts ['font_size'] * 3) . 'px' 
		);
		$icon = '';
		if ($atts ['icon']) {
			if (strpos ( $atts ['icon'], 'icon:' ) !== false) {
				$icon = '<i class="fa fa-' . trim ( str_replace ( 'icon:', '', $atts ['icon'] ) ) . '" style="' . implode ( ';', $icon_style ) . '"></i>';
			} else {
				$icon = '<img alt="" src="' . JURI::root () . '/' . $atts ['icon'] . '" style="' . implode ( ';', $icon_style ) . '"/>';
			}
		}
		
		
		// prepare close button
		$close = '<i class="fa fa-times-circle" style="' . implode ( ';', $icon_style ) . '" onClick="this.parentNode.parentNode.remove()"></i>';
		// prepare style
		$box_style = array (
				'border:' . $atts ['border'],
				'background-color:' . $atts ['background_color'],
				'color:' . $atts ['text_color'],
				'font-size:' . $atts ['font_size'] . 'px',
				'width:' . $atts ['width'] . 'px',
				'line-height:' . ($atts ['font_size'] * 3) . 'px' 
		);
		$return = '<div class="btsc-message-box ' . $atts ['class'] . '" style="' . implode ( ';', $box_style ) . '">
			<div class="btsc-messagebox-before">' . $icon . '</div>
			<div class="btsc-messagebox-message">' . do_shortcode ( $content ) . '</div>
			<div class="btsc-messagebox-after">' . $close . '</div>
		</div>';
		
		// add style
		$css = bt_query_asset ( 'css', 'content-shortcodes' );
		$font = bt_query_asset ( 'css', 'font-awesome.min' );
		return $css . $return;
	}
	public static function carousel($atts = null, $content = null) {
		$mainframe = JFactory::getApplication ();
		$isAdmin = $mainframe->isAdmin ();
		$return = '';
		$atts = shortcode_atts ( array (
				'uid' => '',
				'source' => 'none',
				'gallery' => null,
				'target' => '_self',
				'width' => 0,
				'height' => 100,
				'thumbnail_width' => 180,
				'thumbnail_height' => 120,
				'quality' => 80,
				'crop_center' => true,
				'items_visible' => 3,
				'scroll' => 'page',
				'show_title' => 'yes',
				'align_center' => 'yes',
				'arrows' => 'yes',
				'pagination' => 'no',
				'mousewheel' => 'yes',
				'autoplay' => 3000,
				'speed' => 600,
				'class' => '' 
		), $atts, 'carousel' );
		
		$atts ['responsive'] = $atts ['width'] ? false : true;
		do_shortcode ( $content );
		$slides = self::$images;
		
		// set up data attributes
		$data = '';
		if ($atts ['items_visible']) {
			$data .= ' data-item-per-page="' . $atts ['items_visible'] . '"';
		}
		if ($atts ['scroll']) {
			$data .= ' data-scroll="' . $atts ['scroll'] . '"';
		}
		if ($atts ['arrows'] == 'yes') {
			$data .= ' data-navigation="true"';
		}
		
		if ($atts ['pagination'] == 'none') {
			$data .= ' data-pagination="false"';
		} else if ($atts ['pagination'] == 'number') {
			$data .= ' data-pagination="number"';
		}
		
		$data .= ' data-autoplay="' . $atts ['autoplay'] . '"';
		$data .= ' data-slide-speed="' . $atts ['speed'] . '"';
		// Loop slides
		if (count ( $slides )) {
			// Prepare unique ID
			$id = 'bt-carousel-' . $atts['uid'];
			// Links target
			if ($isAdmin) {
				$atts ['target'] = 'popup';
			}
			if ($atts ['target'] === 'blank') {
				$target = ' target="_blank"';
			} elseif ($atts ['target'] === 'popup') {
				$target = "onclick='window.open(this.href,\"Popup\",\"height=600,width=800,resizable=1,scrollbars=1\"); return false;'";
			} else {
				$target = '';
			}
			
			// Prepare width and height
			$size = ($atts ['responsive']) ? 'width:100%' : 'width:' . intval ( $atts ['width'] ) . 'px; max-width: ' . ($atts ['thumbnail_width'] * $atts ['items_visible']) . 'px;';
			// Open slider
			
			$return = '<div id="' . $id . '" class="btsc-carousel" style="' . $size . ($atts ['align_center'] ? ' margin: auto;' : '') . '" ' . $data . '>';
			
			$count_slide = 0;
			for($i = 0; $i < count ( $slides ); $i ++) {
				// set url image
				$url = parse_url ( $slides [$i] ['src'] );
				if (! isset ( $url ['host'] )) {
					$slides [$i] ['src'] = JURI::root () . $slides [$i] ['src'];
				}
				
				if ($isAdmin) {
					// set link go to article
					$slides [$i] ['link'] = str_replace ( 'administrator/', '', $slides [$i] ['link'] );
				} else {
					// process to create image
					$slides [$i] ['src'] = Bt_shortcodeHelper::createThumb ( $id, $slides [$i] ['src'], $atts ['thumbnail_width'], $atts ['thumbnail_height'], $atts ['crop_center'], $atts ['quality'] );
				}
				
				// open item
				$return .= '<div class="bt-carousel-item" data-src="' . $slides [$i] ['src'] . '" data-title="' . $slides [$i] ['title'] . '" data-desc="" data-link="' . $slides [$i] ['link'] . '">';
				$return .= '<div class="item-image">';
				if ($slides [$i] ['link']) {
					$return .= '<a href="' . $slides [$i] ['link'] . '" ' . $target . '><img src="' . $slides [$i] ['src'] . '" alt=""></a>';
				} else {
					$return .= '<img src="' . $slides [$i] ['src'] . '" alt="">';
				}
				
				if($slides[$i]['title'] && $atts['show_title'] == 'yes'){
					$return .= '<h4 class="image-title">';
					if($slides[$i]['link']){
						$return .= '<a href="' . $slides [$i] ['link'] . '" ' . $target . '>' . $slides[$i]['title'] . '</a>';
					}else{
						$return .= $slides[$i]['title'];
					}
					$return .='</h4>';
				}
				
				// close item
				$return .= '</div></div>';
			}
			// Close slides
			$return .= '</div>';
			
			// Open nav section
			// $return .= '<div class="btsc-carousel-nav">';
			// Append direction nav
			// if ( $atts['arrows'] === 'yes')
			// $return .= '<div class="btsc-carousel-direction"><span class="btsc-carousel-prev"></span><span class="btsc-carousel-next"></span></div>';
			// Append pagination nav
			// $return .= '<div class="btsc-carousel-pagination"></div>';
			// Close nav section
			// $return .= '</div>';
			// Close slider
			// $return .= '</div>';
			
			// reset carousel
			self::$images = array ();
			// self::$image_count['carousel'] = 0;
			
			$style = bt_query_asset('css', 'galleries-shortcodes');
			$style .= bt_query_asset ( 'css', 'owl.carousel' );
			$jquery = bt_query_asset ( 'js', 'jquery' );
			$swiper = bt_query_asset ( 'js', 'owl.carousel.min' );
			$galleriesJs = bt_query_asset ( 'js', 'galleries-shortcodes' );
			$return .= $style . $jquery . $swiper . $galleriesJs;
		} 		// Slides not found
		else
			$return = '<p class="btsc-error">Slider: images not found.</p>';
		return $return;
	}
	public static function taglinebox($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'style' => 'top',
				'width' => 500,
				'font_size' => 14,
				'text_color' => '#5b5b5b',
				'title' => '',
				'title_color' => '#259b9a',
				'align_center' => 'no',
				'bgr_color' => '#f6f6f6',
				'class' => '' 
		), $atts, 'taglinebox' );
		
		$return = '';
		// Title
		if ($atts ['title']) {
			// Style title
			$title_style = array (
					'font-size:' . ($atts ['font_size'] + 4) . 'px',
					'color:' . $atts ['title_color'] 
			);
			$title = '<span class="btsc-taglinebox-title" style="' . implode ( ';', $title_style ) . '">' . $atts ['title'] . '</span>';
		}
		// Border style
		$border_style = array ();
		if ($atts ['style'] === 'top') {
			$border_style = array (
					'border-top:3px solid ' . $atts ['title_color'] . '!important' 
			);
		} elseif ($atts ['style'] === 'full') {
			$border_style = array (
					'border: 3px solid ' . $atts ['title_color'] . '!important' 
			);
		}
		// Box style
		$box_style = array (
				'background-color:' . $atts ['bgr_color'],
				'width:' . $atts ['width'] . 'px',
				'text-align:' . ($atts ['align_center'] === 'yes' ? 'center' : 'left') 
		);
		$box_style = array_merge ( $border_style, $box_style );
		// open wrap
		$return = '<div class="btsc-tagline-box-wrap ' . $atts ['class'] . '" style="' . implode ( ';', $box_style ) . '">';
		$return .= $title;
		// Prepare content style
		$content_style = array (
				'font-size:' . $atts ['font_size'],
				'color:' . $atts ['text_color'] 
		);
		$return .= '<div class="btsc-tagline-box-content" style="' . implode ( ';', $content_style ) . '">' . do_shortcode ( $content ) . '</div>';
		// close wrap
		$return .= '</div>';
		// Load style
		$css = bt_query_asset ( 'css', 'content-shortcodes' );
		return $css . $return;
	}
	public static function testimonials($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				// 'width'=>400,
				'style' => 'default',
				'user_name' => 'Username',
				'company' => 'Company name',
				'user_avatar' => 'icon:user',
				'avatar_size' => '50',
				'font_size' => 12,
				'text_color' => '#838383',
				'bgr_color' => '#ffffff',
				'class' => '' 
		), $atts );
		$return = '';
		// prepare style
		$wrap_style = array (
				'color:' . $atts ['text_color'],
				// 'width:'.$atts['width'].'px',
				'font-size:' . $atts ['font_size'] . 'px',
				'line-height:' . ($atts ['font_size'] * 1.5) . 'px' 
		);
		// create id for box
		$id = uniqid ( 'btsc-testimonials-' );
		// Open box
		$return = '<div id="' . $id . '" class="btsc-testimonials btsc-testimonials-' . $atts ['style'] . ' ' . $atts ['class'] . '" style="' . implode ( ';', $wrap_style ) . '">';
		// prepare style testimonials text
		$testimonialText = '<div class="btsc-testimonials-text" style="background-color:' . $atts ['bgr_color'] . '; ' . ($atts ['style'] == 'left' ? 'margin-left: ' . (2* $atts ['avatar_size'] + 25) . 'px;' : '') . '">' . do_shortcode ( $content ) . '</div>';
		
		// prepare style testimonials Info
		$testimonialInfo = '<div class="btsc-testimonials-info">';
		
		// User avatar
		$testimonialAvatar = '<span class="btsc-testimonials-user-avt">';
		if (strpos ( $atts ['user_avatar'], 'icon:' ) !== false) {
			$testimonialAvatar .= '<i class="fa fa-' . trim ( str_replace ( 'icon:', '', $atts ['user_avatar'] ) ) . '" style="font-size: ' . $atts ['avatar_size'] . 'px;"></i>';
		} else {
			$testimonialAvatar .= '<img alt="" src="' . JURI::root () . '/' . $atts ['user_avatar'] . '" style="width: ' . $atts ['avatar_size'] . 'px; height: ' . $atts ['avatar_size'] . 'px;"/>';
		}
		$testimonialAvatar .= '</span>';
		
		
		// User name
		$testimonialName = '<span class="btsc-testimonials-info-alias">';
		$testimonialName .= '<span class="btsc-testimonials-name">' . $atts ['user_name'] . '</span>';
		if($atts['style'] == 'bottom-right') $testimonialName.= '</br>';
		// Company name
		$testimonialName .= '<span class="btsc-testimonials-company"> ' . $atts ['company'] . '</span>';
		$testimonialName .= '</span>';
		
		if($atts['style'] == 'bottom-right'){
			$testimonialInfo .= $testimonialName . $testimonialAvatar;
		}else if($atts['style'] == 'center'){
			$testimonialInfo .= $testimonialAvatar;
			$testimonialText .= $testimonialName;
		}else{
			$testimonialInfo .= $testimonialAvatar . $testimonialName;
		}
		$testimonialInfo .= '</div>';
		// Close box
		if ($atts ['style'] == 'default' || $atts ['style'] == 'bottom-right') {
			$return .= $testimonialText . $testimonialInfo;
		} else {
			$return .= $testimonialInfo . $testimonialText;
		}
		$return .= '<div style="clear: both;"></div></div>';
		
		// Add declare style
		$declare_css = array ();
		$declare_css ['type'] = 'css';
		$declare_css ['val'] = '#' . $id . ' .btsc-testimonials-text:after{' . 'background-color:' . $atts ['bgr_color'] . ';}';
		
		$declare_style = bt_query_asset ( 'inline', '', $declare_css );
		// Load style
		$css = bt_query_asset ( 'css', 'content-shortcodes' );
		
		return $css . $declare_style . $return;
	}
	public static function skillbars($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				// 'width' => 500,
				'style' => 'default',
				'show_percent' => 'inline',
				'radius' => '10',
				'font_size' => 14,
				'color' => '#919191',
				'achievement_color' => '#2ca7a6',
				'background_color' => '#f4f4f4',
				'class' => '' 
		), $atts );
		do_shortcode ( $content );
		$return = '';
		$bars = array ();
		// prepare style
		$bar_style = array (
				'border-radius: ' . $atts ['radius'] . 'px',
				'background-color:' . $atts ['background_color'] 
		);
		$text_style = array (
				'font-size:' . $atts ['font_size'] . 'px',
				'color:' . $atts ['color'] 
		);
		if (is_array ( self::$bars )) {
			foreach ( self::$bars as $bar ) {
				$html = '<div class="btsc-skillbars-content-skill" style="' . implode ( ';', $text_style ) . '">
					<p class="skill-name">' . $bar ['title'] . '</p>
					<div class="skill" style="' . implode ( ';', $bar_style ) . '">
						<div class="skill-level" style="width:' . $bar ['achievement_percent'] . '%;background-color:' . $atts ['achievement_color'] . '; border-radius: ' . $atts ['radius'] . 'px;" ></div>';
				if ($atts ['show_percent'] != 'none`') {
					$html .= '<div class="skill-percent skill-percent-' . $atts ['show_percent'] . '" style="left: ' . $bar ['achievement_percent'] . '%;">' . $bar ['achievement_percent'] . '%</div>';
				}
				$html .= '</div>
				</div>';
				$bars [] = $html;
			}
			$return = '<div class="btsc-skillbars btsc-skillbars-' . $atts ['style'] . ' ' . $atts ['class'] . '">' . implode ( '', $bars ) . '</div>';
		}
		// reset bars
		self::$bars = array ();
		self::$bar_count = 0;
		// load style
		$style = bt_query_asset ( 'css', 'content-shortcodes' );
		
		return $style . $return;
	}
	public static function skillbar($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'title' => '',
				'achievement_percent' => 0,
				'class' => '' 
		), $atts );
		$x = self::$bar_count;
		self::$bars [$x] = array (
				'title' => $atts ['title'],
				'achievement_percent' => $atts ['achievement_percent'],
				'class' => $atts ['class'] 
		);
		self::$bar_count ++;
	}
	public static function table($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'width' => 0,
				'border' => '1px solid #dfdfdf',
				'font_size_heading' => 16,
				'heading_color' => '#ffffff',
				'bgr_heading_color' => '#259b9a',
				'font_size' => 14,
				'text_color' => '#aeb9bf',
				'bgr_even_row' => 'none',
				'bgr_odd_row' => 'none',
				'class' => '' 
		), $atts );
		$id = uniqid ( 'btsc-table-' );
		// prepare table style
		$declare_css = array (
				'type' => 'css',
				'val' => '' 
		);
		$style = '#' . $id . ' table {
					width:' . ($atts ['width'] == 0 ? '100%' : $atts['width'] . 'px') . '; 
					border:' . $atts ['border'] . ';
				}';
		$style .= '#' . $id . ' table tr:nth-child(odd) td{
					background-color:' . $atts ['bgr_odd_row'] . ';
		}';
		$style .= '#' . $id . ' table tr:nth-child(even) td{
					background-color:' . $atts ['bgr_even_row'] . ';
		}';
		$style .= '#' . $id . ' table th{
					font-size:' . $atts ['font_size_heading'] . 'px;
					color:' . $atts ['heading_color'] . ';
					background-color:' . $atts ['bgr_heading_color'] . ';
					border:' . $atts ['border'] . ';
					text-align: center;
				}';
		$style .= '#' . $id . ' table td{
					font-size:' . $atts ['font_size'] . 'px;
					color:' . $atts ['text_color'] . ';
					border:' . $atts ['border'] . ';
					}';
		$declare_css ['val'] = $style;
		// load style
		$declare_style = bt_query_asset ( 'inline', '', $declare_css );
		$link_style = bt_query_asset ( 'css', 'content-shortcodes' );
		$return = '<div id="' . $id . '" class="btsc-table ' . $atts ['class'] . '">' . do_shortcode ( $content ) . '</div>';
		return $link_style . $declare_style . $return;
	}
	public static function pricetable($atts = null, $content = null) {
		$atts= shortcode_atts(array(
			'width'=>'0',
			'image_position' => 'above',
			'color_scheme'=>'#259b9a',
			'currency'=>'$',
			'title_font_size'=>20,
			'price_font_size'=>30,
			'bgr_price_color'=>'#fafafa',
			'detail_color'=>'#7b7b7b',
			'price_color'=>'#a1adb4',
			'price_hover_color'=>'#ffffff',
			'currency_font_size'=>15,
			'detail_font_size'=>15,
			'purchase_text'=>'PURCHASE',
			'purchase_color'=>'#fff',
			'purchase_bgr_color'=>'#259b9a',
			'bgr_price_hover_color'=>'#259b9a',
			'purchase_text_font_size'=>13,
			'class'=>''
		), $atts);
		$mainframe = JFactory::getApplication();
		$isAdmin = $mainframe->isAdmin();
		
		// not live preview
		if($isAdmin) return 'Preview this shortcode not apply. You can see it in font-end';
		
		// do shortcode content
		do_shortcode($content);
		
		// generator html code
		$pricecols = array();
		$index = 1;
		foreach(self::$pricecols as $col){
			// generator detail col
			$col_detail = array();
			foreach($col['detail'] as $detail){
				if(strpos($detail, 'icon:')===0){
					$col_detail[]='<div class="btsc-pricecol-row detail"><i class="fa fa-'.str_replace('icon:','', $detail).'"></i></div>';
				}else{
					$col_detail[]='<div class="btsc-pricecol-row detail">'.$detail.'</div>';
				}
			}
			// gennerator cols
			$titleAndImage = '<div class="btsc-pricecol-row col-title"><h2>'.$col['title'].'</h2></div>';
			if($col['image']){
				if($atts['image_position'] == 'above'){
					$titleAndImage = '<div class="btsc-pricecol-row image"><img atl="" src="' . $col['image'] .'"/></div>' . $titleAndImage; 
				}elseif($atts['image_position'] == 'below'){
					$titleAndImage .= '<div class="btsc-pricecol-row image"><img atl="" src="' . $col['image'] .'"/></div>';
				}else{
					$titleAndImage = '<div class="btsc-pricecol-row image image-behind"><img atl="" src="' . $col['image'] .'"/>' . $titleAndImage .' </div>';
				}
			}
		 	$pricecols[]= '<div class="btsc-pricecol pricecol-' . ($index++) . ' ' .$col['class'].' col-md-'.round(12/(int)self::$pricecol_count,PHP_ROUND_HALF_DOWN).' col-xs-12 col-sm-6">
		 		<div class="btsc-pricecol-inner">'
		 			. $titleAndImage .
		 			'<div class="btsc-pricecol-row price">
		 				<span class="currency">'.$atts['currency'].'</span>
		 				<span class="price-val">'.$col['price'].'</span>
		 			</div>
		 			'.implode('', $col_detail).
		 			($col['purchase_link']==='' ? '' : 
		 			'<div class="btsc-pricecol-row purchase-button" >'.
		 				($col['purchase_link']===''?'':'<a href="'.$col['purchase_link'].'">'.$atts['purchase_text'].'</a>')
		 			.'</div>') .
		 		'</div>
		 	</div>';
		 }
		  // create id 
		 $id = uniqid('btsc-pricetable-');
		 if($atts['width']){
		 	$width = $atts['width_px'].'px';
		 }else{
		 	$width = 'auto';
		 }
		 $return ='<div id="'.$id.'" class="btsc-pricetable '.$atts['class'].'" style="width:'.$width.';"><div class="row">
		 			'.implode('',$pricecols).'
		 		  </div></div>';
		 
		 // prepare style
		 $title_style = array('color:'.$atts['color_scheme'],'font-size:'.$atts['title_font_size'].'px');
		 $price_style = array('color:'.$atts['price_color'],'background-color:'.$atts['bgr_price_color']);
		 $price_style_special = array('background-color:'.$atts['bgr_price_hover_color'],'color:'.$atts['price_hover_color'].';');	
		 $detail_style = array('font-size:'.$atts['detail_font_size'].'px','color:'.$atts['detail_color']);
		 $purchase_style = array('font-size:'.$atts['purchase_text_font_size'].'px','background-color:'.$atts['purchase_bgr_color'],'color:'.$atts['purchase_color']);
		 
		
		 $inline_style = array('type'=>'css','val'=>'');
		 $inline_style['val'] ='';//'#'.$id.' .btsc-pricecol{width:'.(100/(int)self::$pricecol_count).'%;}';
		 $inline_style['val'].='#'.$id.' .btsc-pricecol h2{'.implode(';', $title_style).'}';
		 $inline_style['val'].= '#'.$id.' .btsc-pricecol .price{'.implode(';', $price_style).'}  ';
		 $inline_style['val'].= '#'.$id.' .btsc-pricecol-special .price{'.implode(';', $price_style_special).'}';
		 $inline_style['val'].= '#'.$id.' .btsc-pricecol .price .price-val{font-size:'.$atts['price_font_size'].'px}';
		 $inline_style['val'].= '#'.$id.' .btsc-pricecol .price .currency{font-size:'.$atts['currency_font_size'].'px}';
		 $inline_style['val'].= '#'.$id.' .btsc-pricecol .detail{'.implode(';', $detail_style).'}';
		 $inline_style['val'].= '#'.$id.' .btsc-pricecol .purchase-button a{'.implode(';', $purchase_style).'}';
		 //$inline_style['val'].= '#'.$id.' .btsc-pricecol .purchase-button{line-height:'.($atts['purchase_text_font_size']*2).'px;min-height:'.(35 + 3*($atts['purchase_text_font_size']+3)).'px;}';
		 $inline_style['val'].= '#'.$id.' .btsc-pricecol .purchase-button{line-height:'.($atts['purchase_text_font_size']*2).'px;}';
		 // load style
		$inline_css = bt_query_asset('inline', '' ,$inline_style);
		$style = bt_query_asset('css', 'bootstrap');
		$style = bt_query_asset('css', 'content-shortcodes');

		$awesomeFont = bt_query_asset('css', 'font-awesome.min');
		// Load js
		$jquery = bt_query_asset('js', 'jquery');
		$js = bt_query_asset('js','content-shortcodes');
		
		// reset count cols & array cols
		self::$pricecols = array();
		self::$pricecol_count = 0;
		return $style.$inline_css.$jquery.$jquery.$return;
	}
	public static function pricecol($atts=null,$content=null){
		$atts= shortcode_atts(array(
			'title'=>'',
			'price'=>'',
			'detail'=>'',
			'purchase_link'=>'',
			'image' => '',
			'class'=>''
		), $atts);
		$detail = explode(';', $atts['detail']);
		$x= self::$pricecol_count;
		self::$pricecols[$x] = array(
			'title'=>$atts['title'],
			'price'=>$atts['price'],
			'detail'=>$detail,
			'image' => $atts['image'],
			'purchase_link'=>$atts['purchase_link'],
			'class'=>$atts['class']
		);
		self::$pricecol_count++;
	}
	public static function accordion($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'width' => 0,
				'active_first' => 1,
				'icon' => 'plus',
				'class' => '' 
		), $atts );
		
		$style = $atts ['width'] ? 'style="width: ' . $atts ['width'] . 'px;"' : '';
		$str = do_shortcode ( $content );
		$html = '<div class="bt-accordion bt-accordion-' . $atts ['icon'] . ' ' . $atts ['class'] . '" data-active-first="' . $atts ['active_first'] . '" ' . $style . '>';
		$html .= $str;
		$html .= '</div>';
		
		// load css
		$css_asset = bt_query_asset ( 'css', 'box-shortcodes' );
		
		// load jquery 1.10
		$jquery_asset = bt_query_asset ( 'js', 'jquery' );
		
		$mainframe = JFactory::getApplication ();
		if ($mainframe->isAdmin ()) {
			// load js for tabs
			$js_asset = bt_query_asset ( 'js', 'shortcodes' );
		} else {
			// load js for tabs
			$js_asset = bt_query_asset ( 'js', 'shortcode-frontend' );
		}
		return $css_asset . $jquery_asset . $js_asset . $html;
	}
	public static function spoiler($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'title' => 'Spoiler Title',
				'icon' => '' 
		), $atts );
		$icon = $atts ['icon'] ? '<i class="fa fa-' . $atts ['icon'] . '"></i>' : '';
		$html = '<div class="bt-spoiler">';
		$html .= '	<div class="bt-spoiler-title">' . $icon . $atts ['title'] . '<span class="bt-spoiler-collapse"></span></div>';
		$html .= '	<div class="bt-spoiler-content">' . $content . '</div>';
		$html .= '</div>';
		
		return $html;
	}
	public static function quote($atts = null, $content = null) {
		$atts = shortcode_atts ( array (
				'style' => 'default',
				'width' => 0,
				'author' => '',
				'author_url' => '',
				'class' => '' 
		), $atts );
		$style = $atts ['width'] ? ' style="width: ' . $atts ['width'] . 'px;" ' : '';
		$html = '<div class="bt-quote bt-quote-' . $atts ['style'] . ' ' . $atts ['class'] . '" ' . $style . '>';
		$html .= ' <div class="bt-quote-inner">';
		if($atts['style'] == 'box'){
			$html .='<i class="fa fa-quote-left bt-quote-box-icon"></i>';
		}
		$html .='<p>' . $content . '</p>';
		if ($atts ['author']) {
			if ($atts ['author_url']) {
				$html .= '<a class="bt-quote-author" href="' . $atts ['author_url'] . '"><i class="fa fa-user"></i>' . $atts ['author'] . '</a>';
			} else {
				$html .= '<span class="bt-quote-author"><i class="fa fa-user"></i>' . $atts ['author'] . '</span>';
			}
		}
		$html .= '</div></div>';
		
		$css_asset = bt_query_asset ( 'css', 'box-shortcodes' );
		$css_asset .= bt_query_asset ( 'css', 'font-awesome.min' );
		return $html . $css_asset;
	}
	
	public static function dropcap( $atts = null, $content = null ) {
		$atts = shortcode_atts( array(
				'style' => 'default',
				'size'  => 3,
				'text_color' => '#ffffff',
				'background_color' => '#000000',
				'class' => ''
		), $atts, 'dropcap' );
		
		// Calculate font-size
		$em = $atts['size'] * 0.5 . 'em';
		$style = 'font-size:' . $em . '; color: ' . $atts['text_color'] .'; background-color: ' . ($atts['style'] != 'default' ? $atts['background_color'] : 'none') . ';';
		$css =bt_query_asset( 'css', 'content-shortcodes' );
		return $css.'<span class="bt-dropcap bt-dropcap-' . $atts['style'] . ' ' . $atts['class'] .'" style="' . $style . '">' . do_shortcode( $content ) . '</span>';
	}
	
	public static function orderlist($atts = null, $content = null){
		
		$atts = shortcode_atts(array(
				'list_style' => 'o-circle',
				'icon' => 'star',
				'class' => ''
		), $atts, 'list');
		$list = do_shortcode($content);
		$count = substr_count($list, '%s');
		$arr = array();
		$return = '<ul class="bt-list '. $atts['class'] .' bt-list-' . $atts['list_style']. '">';
		if($atts['list_style'] == 'icon'){
			if (strpos($atts ['icon'], 'icon:') !== false) {
				$atts['icon'] = str_replace('icon:', '', $atts['icon']);
				$icon = '<i class="bt-list-icon fa fa-' . $atts ['icon'] . '"></i>';
			}else{
				$icon = '<img width="1em" alt="" src="' . $atts['icon'] . '"/>';
			}
			
			for($i = 0; $i< $count; $i++){
				$arr[] = $icon;
			}
			
			$return .= vsprintf($list, $arr);
			
		}else if($atts['list_style'] == 'm-decimal'){
			for($i = 0; $i< $count; $i++){
				$arr[] = '<span class="bt-list-icon bt-list-icon-circle">' . ($i + 1) . '</span>';
			}			
			$return .= vsprintf($list, $arr);			
		}else if($atts['list_style'] == 'm-alphabet'){
			for($i = 0; $i< $count; $i++){
				$arr[] = '<span class="bt-list-icon bt-list-icon-circle">' . chr($i + 65) . '</span>';
			}
			$return .= vsprintf($list, $arr);	
		}else{
			for($i = 0; $i< $count; $i++){
				$arr[] = '';
			}
			$return .= vsprintf($list, $arr);
			
		}
		$return .='</ul>';
		$css =bt_query_asset( 'css', 'content-shortcodes' );
		return $css.$return;
	}
	public static function li($atts, $content){
		$atts = shortcode_atts(array(
				'class' => ''
		), $atts, 'li');
		
		return '<li ' . ($atts['class'] ? ' class="' . $atts['class'] . '"' : '') . '>' . '%s' . do_shortcode($content) . '</li>';
	}
	
	public static function  columns($atts = null, $content = null){
		$atts = shortcode_atts(array(
			'class' => ''
		), $atts, 'columns');
		
		return bt_query_asset('css', 'bootstrap.min'). '<div class="bt-columns ' . $atts['class']. '"><div class="row">' . do_shortcode($content) . '</div></div>';
		
	}
	
	public static function column($atts = null, $content = null){
		$atts = shortcode_atts(array(
				'class' => ''
		), $atts, 'column');
		
		return '<div class="' . $atts['class'] . '">' . do_shortcode($content) . '</div>';
	}
	
	public static function label($atts = null, $content = null){
		$atts = shortcode_atts(array(
				'background_color' => '#09c',
				'font_size'		=> 14,
				'text_color'	=> '#ffffff',
				'class'			=> ''
		), $atts, 'label');
		
		
		return '<span class="bt-label ' . $atts['class'] . '" style="padding: 2px 3px; display: inline-block; border-radius: 3px; background: ' . $atts['background_color'] . '; font-size: ' . $atts['font_size'] . 'px; color: ' . $atts['text_color'] . ';">' 
				. do_shortcode($content) . '</span>';
	}
	
	public static function tooltip($atts = null, $content = null){
		$atts = shortcode_atts(array(
				'tooltip_text' 		=> 'Tooltip Text',
				'position'			=> 'above',
				'style'				=> 'light',
				'font_size'			=> '10',
				'shadow'			=> 'yes',
				'rounded_corner'	=> 'yes',
				'tooltip_class'		=> '',
				'text_color'		=> '#f20000',
				'class'				=> ''				
		), $atts, 'tooltip');
		$data = ' data-tooltip="' . $atts['tooltip_text'] . '"';
		
		if($atts['position'] == 'above'){
			$data .= ' data-at="top center" data-my="bottom center"';
		}else{
			$data .= ' data-at="bottom center" data-my="top center"';
		}
		
		$data .= ' data-classes="qtip-' . $atts['style'] . ($atts['shadow'] == 'yes' ? ' qtip-shadow' : '') .  ($atts['rounded_corner'] == 'yes' ? ' qtip-rounded' : '') . ' bt-tooltip-size-' . $atts['font_size'] . ($atts['tooltip_class'] ? ' ' . $atts['tooltip_class'] : '') . '" ';
		
		
		$class = 'bt-tooltip ' . $atts['class'];
		 
		$return = '<span style="color: ' . $atts['text_color'] . '" class="' . $class . '" ' . $data . '>' . do_shortcode($content) . '</span>';
		$assets = bt_query_asset('css', 'jquery.qtip.min');
		$assets .= bt_query_asset('css', 'content-shortcodes');
		$assets .= bt_query_asset('js', 'jquery');
		$assets .= bt_query_asset('js', 'jquery.qtip.min');
		$assets .= bt_query_asset('js', 'content-shortcodes');
		return $assets. $return;
	}
	
	public static function actionbox($atts = null, $content = null){
		$atts = shortcode_atts(array(
				'width' => 0,
				'style'	=> 'default',
				'title'	=> '',
				'sub_text'	=> '',
				'button_text'	=> '',
				'button_link' 	=> '#',
				'button_target' => '_blank',
				'class'		=> ''
		), $atts, 'actionbox');
		$style = $atts['width'] ? ' style="width:' . $atts['width'] . 'px;" ' : ''; 
		$html = '<div class="bt-actionbox bt-actionbox-' . $atts['style'] . ' ' . $atts['class'] . '"' . $style . '>';
		
		$button = '<div class="bt-actionbox-button">';
		
		if($atts['style'] != 'default'){
			$span= '<span class="bt-actionbox-button-icon"><i class="fa fa-arrow-circle-right"></i></span> <span class="bt-actionbox-button-text">' . $atts['button_text'] .'</span>';
		}else{
			$span= '<span class="bt-actionbox-button-text">' . $atts['button_text'] .'</span>';
		}
		$button .= '<a href="' . $atts ['button_link'] . '" target="_' . $atts ['button_target'] . '">' . $span . '</a>';
		$button .= '</div>';
		$text = '<div class="bt-actionbox-content">';
		$text .= '<h3>' . $atts['title'] . '</h3>';
		$text .= '<p class="bt-actionbox-sub">' . $atts['sub_text'] . '</p>';
		$text .= '</div>';
		
		if($atts['style'] != 'center'){
			$html.= $button . $text;
		}else{
			$html .= $text. $button;
		}
		$html .= '</div>';
		$assets = bt_query_asset ( 'css', 'content-shortcodes' ) . bt_query_asset ( 'css', 'font-awesome.min' );
		return $assets.$html; 
	}
	
	public static function googlemap($atts, $content){
		$atts = shortcode_atts(array(
			'width' => 0,
			'height' => 400,
			'address' => 'New York',
			'coordinate' => '40.7127840,-74.0059410',
			'infowindow' => '',
			'border' => '1px solid #cccccc',
			'zoom' => 13,
			'zoom_control' => 'yes',
			'pan_control' => 'yes',
			'streetview_control' => 'yes',
			'maptype_control' => 'yes',
			'scale_control' => 'yes',
			'overview_control' => 'yes',
			'class' => ''
			), $atts, 'googlemap');
			
		$id = uniqid('btsc-googlemap-');
		$style = ' style="width:' . ($atts['width'] ? $atts['width'] . 'px' : 'auto') . '; height: ' . $atts['height'] . 'px; border: ' . $atts['border']. '"'; 
		$html =  '<div class="btsc-googlemap ' . $atts['class']. '"  id="' . $id . '" ' . $style . '></div>';
		
		$markerScript = '';
		if($atts['address'] || $atts['coordinate']){
			if($atts['coordinate']){				
				$markerScript .= '
				var pos = new google.maps.LatLng(' . $atts['coordinate'] . ');		
				mapOptions.center = pos;
				var map = new google.maps.Map(document.getElementById("' . $id . '"), mapOptions);
				var marker = new google.maps.Marker({map: map, position: pos, title: "' . $atts['infowindow'] . '"});
				';

			}else{
				$markerScript .= '
				var geocoder = new google.maps.Geocoder();
				var pos;
				geocoder.geocode({ "address": " ' . $atts['address'] . '"}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						pos = results[0].geometry.location;	
						mapOptions.center = pos;
						var map = new google.maps.Map(document.getElementById("' . $id . '"), mapOptions);
						var marker = new google.maps.Marker({map: map, position: pos, title: "' . $atts['infowindow'] . '"});
					}else{
						alert("Geocode was not successful for the following reason: " + status + "! Map address: ' . $atts['address'] . '");
					}
				
				});
				
				';
				
			}
		}
		$script = 
		'<script type="text/javascript">
			jQuery(document).ready(function($){
				var mapOptions = {
					zoom					: ' . $atts['zoom'] . ',
					zoomControl				: ' . ($atts['zoom_control'] == 'yes' ? 'true' : 'false') . ',
					scaleControl			: ' . ($atts['scale_control'] == 'yes' ? 'true' : 'false') . ',
					mapTypeControl			: ' . ($atts['maptype_control'] == 'yes' ? 'true' : 'false') . ',
					panControl				: ' . ($atts['pan_control'] == 'yes' ? 'true' : 'false') . ',
					streetViewControl		: ' . ($atts['streetview_control'] == 'yes' ? 'true' : 'false') . ',
					overviewMapControl		: ' . ($atts['overview_control'] == 'yes' ? 'true' : 'false') . ',
				}
				
									
				'
				. $markerScript . '
				
				
			})
		</script>';
		$document = JFactory::getDocument();
		$language = JFactory::getLanguage();
		$mapApi= '//maps.google.com/maps/api/js?sensor=true&language='.$language->getTag();
		$document->addScript($mapApi);
		
		return $html . $script;
	}
	
	
	public static function youtube($atts, $content){
		$atts = shortcode_atts(
				array(
					'url' 		=> '',
					'width'		=> 600,
					'height'	=> 400,
					'responsive'	=> 'yes',
					'autoplay'	=> 'no',
					'class'		=> ''	
				),
				$atts, 'youtube');
		
		if ( !$atts['url'] ) return JText::_('Please insert an URL');

		$id = ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $atts['url'], $match ) ) ? $match[1] : false;
		// Check that url is specified
		if ( !$id ) return JText::_('Please enter correct url');
		// Prepare autoplay
		$autoplay = ( $atts['autoplay'] == 'yes' ) ? '?autoplay=1' : '';
		// Create player
		$return= '<div class="bt-youtube ' . ($atts['responsive'] == 'yes' ? 'bt-media-responsive ' : '')  .$atts['class'] . '">';
		$return.= '<iframe width="' . $atts['width'] . '" height="' . $atts['height'] . '" src="http://www.youtube.com/embed/' . $id . $autoplay . '" frameborder="0" allowfullscreen="true"></iframe>';
		$return.= '</div>';
		$return= bt_query_asset('css', 'media-shortcodes') . $return;
		return $return;
	}
	
	public static function vimeo($atts, $content){
		$atts = shortcode_atts(
				array(
					'url' 		=> '',
					'width'		=> 600,
					'height'	=> 400,
					'responsive'	=> 'yes',
					'autoplay'	=> 'no',
					'class'		=> ''	
				),
				$atts, 'vimeo');
		
		if ( !$atts['url'] ) return JText::_('Please insert an URL');

		$id = ( preg_match( '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $atts['url'], $match ) ) ? $match[count($match) - 1] : false;

		// Check that url is specified
		if ( !$id ) return JText::_('Please enter correct url');
		// Prepare autoplay
		$autoplay = ( $atts['autoplay'] == 'yes' ) ? '&autoplay=1' : '';
		// Create player
		$return= '<div class="bt-vimeo ' . ($atts['responsive'] == 'yes' ? 'bt-media-responsive ' : '')  .$atts['class'] . '">';
		$return.= '<iframe width="' . $atts['width'] . '" height="' . $atts['height'] . '" src="http://player.vimeo.com/video/' . $id . '?title=0&byline=0&portrait=0&color=ffffff' . $autoplay . '" frameborder="0" allowfullscreen="true"></iframe>';
		$return.= '</div>';
		$return = bt_query_asset('css', 'media-shortcodes') . $return;
		return $return;
	}
	
	public static function video($atts, $content){
		$atts = shortcode_atts(
				array(
					'url' 			=> '',
					'poster'		=> '',
					'width'			=> 600,
					'height'		=> 400,
					'responsive'	=> 'yes',
					'autoplay'		=> 'no',
					'control'		=> 'yes',
					'loop'			=> 'no',
					'class'			=> ''	
				),
				$atts, 'video');
		if ( !$atts['url'] ) return JText::_('Please insert absolute video URI');
		$ext = substr($atts['url'], strrpos($atts['url'], '.') + 1);
		if(!in_array($ext, array('mp4', 'webm', 'ogg', 'MP4', 'WEBM', 'OGG'))){
			return JText::_('Your video\'s format isn\'t supported. Please use video in these formats: MP4, WEBM and OGG.');
		}
		$return = '<div class="bt-video ' . ($atts['responsive'] == 'yes' ? 'bt-video-responsive ' : '') . $atts['class'] . '">';
		$return .= '<video width="' . $atts['width'] . '" height="' . $atts['height'] . '" poster="' . JURI::root() . '/' . $atts['poster']. '" ';
		if($atts['control'] == 'yes'){
			$return .= 'controls="controls" ';
		}
		if($atts['loop'] == 'yes'){
			$return .= 'loop="loop"';
		}
		if($atts['autoplay'] == 'yes'){
			$return .= 'autoplay="autoplay"';
		}
		$return .= '><source src="' . $atts['url'] .'" type="video/' . $ext . '">Your browser does not support the video tag</video></div>';
		$return = bt_query_asset('css', 'media-shortcodes') . $return;
		return $return;
		
	}
}
