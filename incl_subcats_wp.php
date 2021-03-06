<?php
$file = 'http://' . $mn_agent_url . '/' . $mn_agent_folder . '/mannanetwork-dir/get_category_json.php';

/* Dev Note PHPCS reports "ERROR   | Processing form data without nonce verification" for all these POSt variables but this page is included and the nonce verification was done on the previous page */

if ( isset( $_POST['main_cat_nonce'] ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_POST['main_cat_nonce'] ) );
} elseif ( isset( $_GET['main_cat_nonce'] ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_GET['main_cat_nonce'] ) );
} else {
	$nonce = 'null';
}



	$response = wp_remote_post(
		$file,
		array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => array(
				'selected_cat_id' => $category_id,
'type' => 'categories',
			),
			'cookies'     => array(),
		)
	);

	if ( is_wp_error( $response ) ) {
		$error_message = esc_attr( $response->get_error_message() );
		echo 'Something went wrong: (' . esc_attr( $error_message ) . ')';
	} else {
		$category_list = json_decode( $response['body'], true );

echo '
<script>
var main_cat_nonce = "'.esc_attr( $nonce ).'"
var original_cat_id = "'.esc_attr( $selected_cat_id ) . '"
</script>';
		echo '<span id="mn_subcat_container"> 
<form action=""> <table id="mn_subcat_table">
<tr><td style="width:45%;">
<select name="category_menu" onchange="updategoButton(this.value,\'false\',\'' . esc_attr( $nonce ) . '\',\'' . esc_attr( $selected_cat_id ) . '\'), showSubLoc1(this.value,\'' . esc_attr( $nonce ) . '\',1,\'' . esc_attr( $selected_cat_id ) . '\',\'categories\')"><option value="">' . esc_attr( WORDING_AJAX_MENU1 ) . '</option> ';
		echo "<option value='y:" . esc_attr( $selected_cat_id ) . ":'></option>";
		foreach ( $category_list as $key => $value ) {
			if ( $category_list[ $key ]['lft'] + 1 < $category_list[ $key ]['rgt'] ) {
				echo "<option value='y:" . esc_attr( $category_list[ $key ]['id'] ) . ':' . esc_attr( $category_list[ $key ]['name'] ) . "'>" . esc_attr( $category_list[ $key ]['name'] ) . '</option>';
			} else {
				echo "<option value='n:" . esc_attr( $category_list[ $key ]['id'] ) . ':' . esc_attr( $category_list[ $key ]['name'] ) . "'>" . esc_attr( $category_list[ $key ]['name'] ) . '</option>';
			}
		}
		echo '</select>
		      <div class="catHint1" id="catHint1" name="catHint1"><b>' . esc_attr( WORDING_AJAX_1 ) . '</b></div><input type="hidden" id="category_name" name="category_name" class ="category_name" value="">
<input type="hidden" id="$selected_cat_id" name="$selected_cat_id" class ="$selected_cat_id" value=""><!--</div>--></td></tr></table></form></span>	';
	}

