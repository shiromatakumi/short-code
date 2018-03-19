<?php
/*
Plugin Name: Short Code for Blog
Description: 色々なショートコードを集めたプラグインです
Version: 1.0
Author: Takumi Shiroma
Author URI: 
License: GPL2
*/

/* 
	prefix is 'retv'
	'retv' means 'Return Value'
*/

define( 'SHORT_CODE_FOR_BLOG_DIR', plugin_dir_path( __FILE__ ) );

require_once( SHORT_CODE_FOR_BLOG_DIR . 'short-code-for-blog-editor.php');

/**
 * YouTube軽量化 [sc-youtube id="" alt=""]
 */
if(!function_exists( 'retv_youtube_speed_up')) {
	function retv_youtube_speed_up($atts) {
		$yt_id = $atts['id'];
		$yt_alt = $atts['alt'];
		$html = '<div class="retv-youtube-wrap"><div class="retv-youtube" data-video="https://www.youtube.com/embed/'. $yt_id .'?autoplay=1"><img src="https://img.youtube.com/vi/'. $yt_id .'/hqdefault.jpg" alt="' . $yt_alt . '"></div></div>';
		return $html;
	}
}
add_shortcode( 'sc-youtube', 'retv_youtube_speed_up');

if(!function_exists( 'retv_youtube_add_style' )) {
	function retv_youtube_add_style() {
		$yt_css = '<style>.retv-youtube-wrap{padding:30px 0;text-align:center;background-color:#000;margin-bottom:10px}.retv-youtube-wrap iframe{margin:0}.retv-youtube{display:inline-block;position:relative;margin-bottom:6px;width:480px;height:270px;overflow:hidden;vertical-align:bottom;margin-bottom:0;cursor:pointer}.retv-youtube img{position:relative;top:50%;left:50%;width:auto;height:auto;vertical-align:bottom;-webkit-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}.retv-youtube::after,.retv-youtube::before{position:absolute;content:"";top:50%;left:50%}.retv-youtube::before{width:64px;height:44px;background-color:#CC181E;margin-left:-32px;margin-top:-22px;border-radius:12px;z-index:10;opacity:.9;transition:all .3s}.retv-youtube::after{margin-top:-10px;margin-left:-8px;border-top:solid 10px transparent;border-bottom:solid 10px transparent;border-right:solid 18px transparent;border-left:solid 18px #fff;z-index:20}@media screen and (max-width: 560px){.retv-youtube-wrap{padding:0;background-color:transparent}.retv-youtube::before{border-radius:10px;width:50px;height:36px;margin-left:-28px;margin-top:-18px}.retv-youtube::after{margin-top:-8px;margin-left:-10px;border-top:solid 8px transparent;border-bottom:solid 8px transparent;border-right:solid 16px transparent;border-left:solid 16px #fff}}@media screen and (max-width: 488px){.retv-youtube{width:100%;max-width:480px;height:56.25%}}</style>';
		echo $yt_css;
	}
}
add_action( 'wp_head', 'retv_youtube_add_style' );

if(!function_exists( 'retv_youtube_add_script' )) {
	function retv_youtube_add_script() {
		$yt_script = '<script>!function(){for(var e=document.getElementsByClassName("retv-youtube"),t=0;t<e.length;t++)e[t].addEventListener("click",function(){video=\'<iframe src="\'+this.getAttribute("data-video")+\'" frameborder="0" width="480" height="270"></iframe>\',this.outerHTML=video})}();</script>';
	    echo $yt_script;
	}
}
add_action( 'wp_footer', 'retv_youtube_add_script' );

/**
 * 内部リンクをブログカードにする [sc-blogcard id=""] [sc-blogcard slug=""]
 */
if( !function_exists( 'retv_blogcard_link' )) {
	function retv_blogcard_link($atts) {
		$link_url; $thumb_url; $article_title;
		$link_id = false;
		$no_link = '<p>記事がありません。</p>';
		// リンク先のID
		if( isset($atts['id']) ){
			$link_id = $atts['id'];
		} elseif( isset($atts['slug']) ){
			$post_id = get_page_by_path($atts['slug'], "OBJECT", "post");
			$link_id = $post_id->ID;
		} else {
			return '<p>IDがありません</p>';
		}
		// IDからリンク先のURLを取得
		if( get_permalink( $link_id ) ) {
			$link_url = get_permalink( $link_id );
		} else {
			return '<p>リンク先のURLが見つかりません。</p>';
		}
		// 
		if($link_id) {
			$article_title = get_the_title( $link_id );
			$thumb_url = get_the_post_thumbnail_url( $link_id , 'medium');
			if ($thumb_url === '') {
				$thumb_url = SHORT_CODE_FOR_BLOG_DIR . 'img/noimage.jpg';
			}
		} else {
			return '<p>リンク先のIDが見つかりません。</p>';
		}
		$excerpt = retv_get_the_excerpt( $link_id );
		return '<div class="retv_blogcard"><a href="' . $link_url . '" class="retv_blogcard__thumbnail-link"><div class="retv_blogcard__thumbnail" style="background-image: url(' . $thumb_url . ');"></div></a><div class="retv_blogcard_text"><p class="retv_blogcard__title"><a href="' . $link_url . '">' . $article_title . '</a></p><p class="retv_blogcard__excerpt">' . $excerpt . '</p></div></div>';
	}
}
add_shortcode( 'sc-blogcard', 'retv_blogcard_link');

function retv_get_the_excerpt($post_id){
	$excerpt;
	$post = get_post( $post_id );
	if($post->post_excerpt) {
		$excerpt = $post->post_excerpt;
	} else {
		$excerpt = $post->post_content;
		$excerpt = strip_tags($excerpt, true);
		$excerpt = strip_shortcodes($excerpt, true);
	}
	if( strlen($excerpt) > 80) {
		$excerpt = mb_strimwidth($excerpt, 0, 80, '...', 'UTF-8');	
	}
	return $excerpt;
}

if(!function_exists( 'retv_blogcard_add_style')) {
	function retv_blogcard_add_style() {
		$blogcard_css = '
<style>
.retv_blogcard {
	margin-bottom: 16px;
	padding: 14px;
	border: 1px solid #ddd;
	display: -webkit-flex;
	display: flex;
}
.retv_blogcard__thumbnail {
	width:150px;
	height:150px;
	background-color: #ddd;
	background-size: cover;
	background-position: center;
}
.retv_blogcard .retv_blogcard__thumbnail-link {
	margin-right: 16px;
}
.retv_blogcard .retv_blogcard__thumbnail-link,
.retv_blogcard .retv_blogcard__thumbnail-link:hover {
	border: none;
	box-shadow: none;
}
.retv_blogcard .retv_blogcard__title {
	margin-bottom: 10px;
	font-weight: bold;
}
.retv_blogcard .retv_blogcard__excerpt {
	font-size: .8rem;
	color: #666;
	margin-bottom: 0;
}
@media (max-width: 500px) {
	.retv_blogcard {
		padding: 10px;
	}
	.retv_blogcard .retv_blogcard__thumbnail-link {
		width:100px;
		height:100px;
		margin-right: 10px;
	}
}</style>';
		echo $blogcard_css;
	}
}
add_action( 'wp_head', 'retv_blogcard_add_style' );

/**
 * リンクをボタンにするショートコード [sc-button] [/sc-button]
 */
if( !function_exists( 'retv_button_link' )) {
	function retv_button_link( $atts, $content = "" ) {
		$class = isset($atts['class']) ? $atts['class'] : '';
		$align = isset($atts['align']) ? ' align-' . $atts['align'] : '';
		return '<div class="retv_button-link ' . $class . $align .'">' . $content . '</div>';
	}
}
add_shortcode( 'sc-button', 'retv_button_link');

if( !function_exists( 'retv_flat_link' )) {
	function retv_flat_link( $atts, $content = "" ) {
		$class = isset($atts['class']) ? ' ' . $atts['class'] : '';
		$align = isset($atts['align']) ? ' align-' . $atts['align'] : '';
		return '<div class="retv_flat-link' . $class . $align .'">' . $content . '</div>';
	}
}
add_shortcode( 'sc-flat', 'retv_flat_link');

if(!function_exists( 'retv_button_link_add_style' )) {
	function retv_button_link_add_style() {
		$button_css = '<style>
.retv_button-link {
	margin-bottom: 20px;
}
.retv_button-link.align-center {
	text-align: center;
}
.retv_button-link a {
	position: relative;
	display: inline-block;
	padding: 12px 24px;
	background-color: #dc1826;
	color: #fff;
	text-decoration: none;
	box-shadow: 0 4px #781c23;
	-webkit-transition: all .3s;
	transition: all .3s;
	-webkit-transform: translateY(0px);
	transform: translateY(0px);
	border-radius: 4px;
	margin: 0 auto;
}
.retv_button-link.blue a {
	background-color: #2f54c6;
	box-shadow: 0 4px #1a3589;
}
.retv_button-link.orange a {
	background-color: #ea7f1a;
	box-shadow: 0 4px #ad641f;
}
.retv_button-link.light a {
	background-color: #d76068;
	box-shadow: 0 4px #b1353e;
}
.retv_button-link.lightblue a {
	background-color: #5682cd;
	box-shadow: 0 4px #3964ac;
}
.retv_button-link.lightorange a {
	background-color: #ef9948;
	box-shadow: 0 4px #b56c26;
}
.retv_button-link.shiny a::before {
	content: "";
	position: absolute;
	width: 100%;
	height: 50%;
	background-color: #fff;
	opacity: .2;
	top: 0;
	left: 0;
	-webkit-transition: all .3s;
	transition: all .3s;
}
.retv_button-link a:hover {
	box-shadow: 0 0 #111;
	-webkit-transform: translateY(4px);
	transform: translateY(4px);
}
.retv_button-link.shiny a:hover::before {
	opacity: 0;
}
.retv_flat-link a {
	display: inline-block;
	padding: 12px 24px;
	background-color: #dc1826;
	color: #fff;
	text-decoration: none;
	border-radius: 4px;
}
.retv_flat-link.light a {
	background-color: #d76068;
}
</style>';
		echo $button_css;
	}
}
add_action( 'wp_head', 'retv_button_link_add_style' );

/**
 * ボックスで囲むショートコード [sc-box] [/sc-box]
 */
if( !function_exists( 'retv_wrap_box' )) {
	function retv_wrap_box( $atts, $content = "" ) {
		$class = isset($atts['class']) ? $atts['class'] : '';
		return '<div class="retv_box ' . $class . '">' . $content . '</div>';
	}
}
add_shortcode( 'sc-box', 'retv_wrap_box');

if(!function_exists( 'retv_wrap_box_css' )) {
	function retv_wrap_box_css() {
		$button_css = '<style>
.retv_box {
	margin: 32px 0;
	padding: 18px 16px 14px;
	border: 1px solid #eee;
}
</style>';
		echo $button_css;
	}
}
add_action( 'wp_head', 'retv_wrap_box_css' );