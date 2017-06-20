<?php
/*
 Plugin Name: Groups Codes
Plugin URI: http://www.eggemplo.com
Description: Groups Codes
Author: Antonio Blanco
Version: 1.0
Author URI: http://www.eggemplo.com
*/
class Groups_Codes_Plugin {

	/**
	 * This array contains the pairs: code => group_id
	 * You need to change it with your own codes and groups id.
	 */
	public static $codes_table = array( 
		'0001' => 1,
		'0010' => 2,
		'0011' => 3
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
	}

	public static function wp_init() {
		add_shortcode( 'groups_codes', array( __CLASS__, 'groups_codes_shortcode' ) );
		add_shortcode( 'groups-codes', array( __CLASS__, 'groups_codes_shortcode' ) );
	}

	public static function groups_codes_shortcode( $atts, $content = null ) {
		$codes_table = self::$codes_table;

		$output = "";
		$options = shortcode_atts(
				array(
						'redirect_to' => null
				),
				$atts
		);
		if ( $user_id = get_current_user_id() ) {

			if ( isset( $_REQUEST['submit'] ) ) {
				if ( isset( $_REQUEST['groups_codes_code'] )  && wp_verify_nonce( $_REQUEST['groups_codes_code'], 'groups_codes_form' ) ) {
					if ( isset( $_REQUEST['code'] ) && ( strlen( trim( $_REQUEST['code'] ) ) > 0 ) ) {
						if ( isset( $codes_table[ trim( $_REQUEST['code'] ) ] ) ) {
							$group_id = $codes_table[ trim( $_REQUEST['code'] ) ];
							if ( Groups_User_Group::create( array( "user_id"=>$user_id, "group_id"=>$group_id ) ) ) {
								$group = Groups_Group::read( $group_id );
								$output .= '<p>' . __( 'You have been added to the "' . $group->name . '" group.' ) . '</p>';
							} else {
								$output .= '<p>' . __( 'Invalid code.' ) . '</p>';
							}
						} else {
							$output .= '<p>' . __( 'Invalid code.' ) . '</p>';
						}
					} else {
						$output .= '<p>' . __( 'Invalid code.' ) . '</p>';
					}
				}
			}

			$output .= '<div class="groups-codes-container">';
			$output .= '<form action="">';
			$output .= '<input type="text" name="code" placeholder="' . __( 'Enter your code' ) . '" />';
			$output .= '<input type="submit" name="submit" value="' . __( 'Submit' ) . '" />';
			$output .= wp_nonce_field( 'groups_codes_form', 'groups_codes_code' );
			$output .= '</form>';
			$output .= '</div>';

		} else {
			$output .= __( 'You need to be logged in before giving us a download code.' );
		}

		return $output;
	}
}

Groups_Codes_Plugin::init();
?>
