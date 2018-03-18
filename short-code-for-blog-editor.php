<?php
/*
  input completion
*/

/* 
	prefix is 'retv'
	'retv' means 'Return Value'
*/

if(!function_exists( 'retv_add_quicktags' )) {
	function retv_add_quicktags() {
		if (wp_script_is('quicktags')){?>
			<script>
			  QTags.addButton('retv-qt-youtube','YouTube軽量化','[sc-youtube id="" alt=""]');
			  QTags.addButton('retv-qt-blogcard-id','ブログカード（ID）','[sc-blogcard id=""]');
			  QTags.addButton('retv-qt-blogcard-slug','ブログカード（スラッグ）','[sc-blogcard slug=""]');
			  QTags.addButton('retv-qt-button-link','ボタンリンク','[sc-button][/sc-button]');
			</script>
		<?php }
	}
}
add_action( 'admin_print_footer_scripts', 'retv_add_quicktags' );
