<?php
/*
Plugin Name: YOURLSs Password Protection
Plugin URI: https://mateoc.net/b_plugin/yourls_PasswordProtection/
Description: This plugin enables the feature of password protecting your short URLs!
Version: 1.3
Author: Matthew
Author URI: https://mateoc.net/
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

require_once YOURLS_ABSPATH.'/includes/vendor/autoload.php';

// Hook our custom function into the 'pre_redirect' event
yourls_add_action( 'pre_redirect', 'warning_redirection' );

// Custom function that will be triggered when the event occurs
function warning_redirection( $args ) {		
	$matthew_pwprotection_array = json_decode(yourls_get_option('matthew_pwprotection'), true) ?? [];    // Edit by LouisSung (to prevent #33 from null warnings)
	if ($matthew_pwprotection_array === false) {
		yourls_add_option('matthew_pwprotection', 'null');
		$matthew_pwprotection_array = json_decode(yourls_get_option('matthew_pwprotection'), true);
		if ($matthew_pwprotection_array === false) {
			die("Unable to properly enable password protection due to an apparent problem with the database.");
		}
	}

	$matthew_pwprotection_fullurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$matthew_pwprotection_urlpath = parse_url( $matthew_pwprotection_fullurl, PHP_URL_PATH );
	$matthew_pwprotection_pathFragments = explode( '/', $matthew_pwprotection_urlpath );
	$matthew_pwprotection_short = end( $matthew_pwprotection_pathFragments );
	
	if( array_key_exists( $matthew_pwprotection_short, $matthew_pwprotection_array ) ){
		if( isset( $_POST[ 'password' ] ) && $_POST[ 'password' ] == $matthew_pwprotection_array[ $matthew_pwprotection_short ] ){ //Check if password is submited, and if it matches the DB
			$url = $args[ 0 ];
			header("Location: $url"); //Redirects client
			die();
		} else {
			$error = ( isset( $_POST[ 'password' ] ) ? "<script>alertify.error(\"Incorrect Password, try again\")</script>" : "");
			$matthew_ppu =    yourls__( "Password Protected URL",                       "matthew_pwp" ); //Translate Password Title
			$matthew_ph =     yourls__( "Password"                                    , "matthew_pwp" ); //Translate the word Password
			$matthew_sm =     yourls__( "Please enter the password below to continue.", "matthew_pwp" ); //Translate the main message
			$matthew_submit = yourls__( "GO!"                                         , "matthew_pwp" ); //Translate the Submit button    // Edit by LouisSung (change from send to go)
			//Displays main "Insert Password" area
			// edit: 1. change title: Redirection Notice->
			//       2. add text: `Password is required to continue !`
			//       3. body background: #2c3338->#6b7f8e
			//       4. login form input[type="submit"] background-color: #ea4c88->#004290, hover: #d44179->#006df1, remove: margin-bottom: 2em;
			//       5. login form input[type="text"] margin-bottom:1em->margin:0.7rem 0
			//       6. #login form span add: margin: 0.7rem 0
			//       7. tl 1l4e932k7x87!!
			echo <<<PWP
			<html>
				<head>
					<title>Password is Required for Following Redirection</title>
					<style>
						@import url(https://weloveiconfonts.com/api/?family=fontawesome);
						@import url(https://meyerweb.com/eric/tools/css/reset/reset.css);
						[class*="fontawesome-"]:before {
						  font-family: 'FontAwesome', sans-serif;
						}
						* {
						  -moz-box-sizing: border-box;
							   box-sizing: border-box;
						}
						*:before, *:after {
						  -moz-box-sizing: border-box;
							   box-sizing: border-box;
						}

						body {
						  background: #6b7f8e;
						  color: #606468;
						  font: 1.5rem 'Open Sans', sans-serif;
						  margin: 0;
						}

						a {
						  color: #eee;
						  text-decoration: none;
						}

						a:hover {
						  text-decoration: underline;
						}

						input {
						  border: none;
						  font-family: 'Open Sans', Arial, sans-serif;
						  font-size: 1.7rem;
						  padding: 0;
						  -webkit-appearance: none;
						}
						.clearfix {
						  *zoom: 1;
						}
						.clearfix:before, .clearfix:after {
						  content: ' ';
						  display: table;
						}
						.clearfix:after {
						  clear: both;
						}

						.container {
						  left: 50%;
						  position: fixed;
						  top: 50%;
						  -webkit-transform: translate(-50%, -50%);
							  -ms-transform: translate(-50%, -50%);
								  transform: translate(-50%, -50%);
						}
						#login {
						  width: 30rem;
						}

						#login form span {
						  background-color: #363b41;
						  border-radius: 3px 0px 0px 3px;
						  color: #606468;
						  display: block;
						  float: left;
						  height: 5rem;
						  width: 5rem;
						  line-height: 5rem;
						  text-align: center;
						  margin: 0.7rem 0;
						}

						#login form input[type="text"], input[type="password"] {
						  background-color: #3b4148;
						  border-radius: 0px 3px 3px 0px;
						  color: #606468;
						  margin: 0.7rem 0;
						  padding: 0 16px;
						  height: 5rem;
						  width: 25rem;
						}

						#login form input[type="submit"] {
						  border-radius: 3px;
						  -moz-border-radius: 3px;
						  -webkit-border-radius: 3px;
						  background-color: #004290;
						  color: #eee;
						  font-weight: bold;
						  text-transform: uppercase;
						  height: 5rem;
						  width: 30rem;
						}

						#login form input[type="submit"]:hover {
						  background-color: #006df1;
						}

						#login > p {
						  text-align: center;
						}

						#login > p span {
						  padding-left: 5px;
						}
					</style>
					<!-- JavaScript -->
					<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.11.4/build/alertify.min.js"></script>

					<!-- CSS -->
					<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.4/build/css/alertify.min.css"/>
					<!-- Default theme -->
					<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.11.4/build/css/themes/default.min.css"/>
				</head>
				<body>
					<div class="container">
						<div id="login">
							<form method="post">
								<fieldset class="clearfix">
									<p style="color:#fff;text-align:center;font-size:1.7rem;">Password&nbsp;is&nbsp;required&nbsp;to&nbsp;continue&nbsp;!</p>
									<p><span class="fontawesome-lock"></span><input type="password" name="password" value="Password" onBlur="if(this.value == '') this.value = 'Password'" onFocus="if(this.value == 'Password') this.value = ''" required></p>
									<p><input type="submit" value="$matthew_submit"></p>
								</fieldset>
							</form>
						</div>
					</div>
					$error
				</body>
		</html>
PWP;
			die();
		}
	}
}

// Register plugin page in admin page
yourls_add_action( 'plugins_loaded', 'matthew_pwprotection_display_panel' );
function matthew_pwprotection_display_panel() {
	yourls_register_plugin_page( 'matthew_pwp', 'Password Protection', 'matthew_pwprotection_display_page' );
}

// Function which will draw the admin page
function matthew_pwprotection_display_page() {
	if( isset( $_POST[ 'checked' ] ) && isset( $_POST[ 'password' ] ) || isset( $_POST[ 'unchecked' ] ) ) {
		matthew_pwprotection_process_new();
		matthew_pwprotection_process_display();
	} else {
		if(yourls_get_option('matthew_pwprotection') !== false){
			yourls_add_option( 'matthew_pwprotection', 'null' );
		}
		matthew_pwprotection_process_display();
	}
}

// Set/Delete password from DB
function matthew_pwprotection_process_new() {	
	if( isset( $_POST[ 'checked' ] ) ){
		yourls_update_option( 'matthew_pwprotection', json_encode( $_POST[ 'password' ] ) );
	}
	if( isset( $_POST[ 'unchecked' ] ) ){
		$matthew_pwprotection_array = json_decode(yourls_get_option('matthew_pwprotection'), true); //Get's array of currently active Password Protected URLs
		foreach ( $_POST[ 'unchecked' ] as $matthew_pwprotection_unchecked ){
			unset($matthew_pwprotection_array[ $matthew_pwprotection_unchecked ]);        // Edit by LouisSung (bug fix, add $)
		}
		yourls_update_option( 'matthew_pwprotection', json_encode( $_POST[ 'password' ] ) );
	}
	echo "<p style='color: green'>Success!</p>";
}

//Display Form
function matthew_pwprotection_process_display() {
	$config_db = include_once 'config_db.php';
	// ref: https://github.com/auraphp/Aura.Sql/blob/2.6.0/README.md#lazy-connection-instance
	// Edit by LouisSung (move from $ydb to $pdo according to the warning ($ydb was deprecated since yourls 1.7.3))
	$pdo = new Aura\Sql\ExtendedPdo($config_db['db_info'], $config_db['db_username'], $config_db['db_password']);

	$table = YOURLS_DB_TABLE_URL;
	$query = $pdo->fetchObjects( "SELECT * FROM `$table` WHERE 1=1" );    // Edit by LouisSung (not sure if is correct, though it works :D)

	$matthew_su = yourls__( "Short URL"   , "matthew_pwp" ); //Translate "Short URL"
	$matthew_ou = yourls__( "Original URL", "matthew_pwp" ); //Translate "Original URL"
	$matthew_pw = yourls__( "Password"    , "matthew_pwp" ); //Translate "Password"

	// Edit by LouisSung (move submit button from the bottom to the top of table)
	echo <<<TB
	<style>
	table {
		border-collapse: collapse;
		width: 100%;
	}

	th, td {
		text-align: left;
		padding: 8px;
	}

	tr:nth-child(even){background-color: #f2f2f2}
	tr:nth-child(odd){background-color: #fff}
	</style>
	<div style="overflow-x:auto;">
		<form method="post">
			<input type="submit" value="Submit">
			<table>
				<tr>
					<th>$matthew_su</th>
					<th>$matthew_ou</th>
					<th>$matthew_pw</th>
				</tr>
TB;
	foreach( $query as $link ) { // Displays all shorturls in the YOURLS DB
		$short = $link->keyword;
		$url = $link->url;
		$matthew_pwprotection_array =  json_decode(yourls_get_option('matthew_pwprotection'), true) ?? []; //Get's array of currently active Password Protected URLs    // Edit by LouisSung (to prevent #271 from null warnings)
		if( strlen( $url ) > 51 ) { //If URL is too long it will shorten it
			$sURL = substr( $url, 0, 49 ). "...";
		} else {
			$sURL = $url;
		}
		if( array_key_exists( $short, $matthew_pwprotection_array ) ){ //Check's if URL is currently password protected or not
			$text = yourls__( "Enable " );
			$password = $matthew_pwprotection_array[ $short ];
			$checked = " checked";
			$unchecked = '';
			$style = '';
			$disabled = '';
		} else {
			$text = yourls__( "Disable " );
			$password = '';
			$checked = '';
			$unchecked = ' disabled';
			$style = 'visibility: hidden;';    // Edit by LouisSung (move from `display:none` to `visibility:hidden` to keep the empty space)
			$disabled = ' disabled';
		}

		echo <<<TABLE
				<tr>
					<td>/<a href="/$short" target="_blank">$short</a></td>
					<td><a title="$url" href="$url" target="_blank">$sURL</a></td>
					<td>
						<input type="checkbox" name="checked[{$short}]" class="matthew_pwprotection_checkbox" value="enable" data-input="$short"$checked> $text
						<input type="hidden" name="unchecked[{$short}]" id="{$short}_hidden" value="true"$unchecked>
						<input id="$short" type="password" name="password[$short]" style="margin-left:1rem; $style" value="$password" placeholder="Enter Password..."$disabled>
					</td>
				</tr>
TABLE;
	}    // Edit by LouisSung (move from toggle (display) to ternary operator (visibility))
	echo <<<END
			</table>
		</form>
	</div>
	<script>
		$( ".matthew_pwprotection_checkbox" ).click(function() {
			var dataAttr = "#" + this.dataset.input;
			$( dataAttr ).css('visibility', $( dataAttr ).css('visibility')==='hidden'? 'visible':'hidden');
			if( $( dataAttr ).attr( 'disabled' ) ) {
				$( dataAttr ).removeAttr( 'disabled' );
				
				$( dataAttr + "_hidden" ).attr( 'disabled' );
				$( dataAttr + "_hidden" ).prop('disabled', true);
			} else {
				$( dataAttr ).attr( 'disabled' );
				$( dataAttr ).prop('disabled', true);				
				
				$( dataAttr + "_hidden" ).removeAttr( 'disabled' );
				$( dataAttr + "_hidden" ).removeAttr( 'disabled' );
			}
		});
	</script>
END;
}
?>
