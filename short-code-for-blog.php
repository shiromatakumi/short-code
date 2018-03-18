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
 * 内部リンクをブログカードにする [sc-link id=""] [sc-link slug=""]
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
			$thumb_url = get_the_post_thumbnail( $link_id , 'medium' ,$article_title);
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
	}
	if( strlen($excerpt) > 80) {
		$excerpt = mb_strimwidth($excerpt, 0, 80, '...', 'UTF-8');	
	}
	$excerpt = wp_strip_all_tags($excerpt, true);
	
	return $excerpt;
}

if(!function_exists( 'retv_blogcard_add_style')) {
	function retv_blogcard_add_style() {
		$blogcard_css = '
<style>
.retv_blogcard {
	padding: 14px;
	border: 1px solid #ddd;
	display: -webkit-flex;
	display: flex;
}
.retv_blogcard__thumbnail {
	width:150px;
	height:150px;
	background-color: #ddd;
	margin-right: 16px;
}
.retv_blogcard .retv_blogcard__thumbnail-link,
.retv_blogcard .retv_blogcard__thumbnail-link:hover {
	border: none;
	box-shadow: none;
}
.retv_blogcard .retv_blogcard__title {
	margin-bottom: 10px;
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
	.retv_blogcard .retv_blogcard__thumbnail {
		width:100px;
		height:100px;
		margin-right: 10px;
	}
}</style>';
		echo $blogcard_css;
	}
}
add_action( 'wp_head', 'retv_blogcard_add_style' );



