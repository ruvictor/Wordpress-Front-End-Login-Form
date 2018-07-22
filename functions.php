<?php
/*------------------------------------*\
	Login Form
\*------------------------------------*/
// login action hook - catches form submission and acts accordingly
add_action('init','vr_login');
function vr_login() {
  global $vr_error;
  $vr_error = false;
  if (isset($_POST['username']) && isset($_POST['password'])) {
    $creds = array();
    $creds['user_login'] = $_POST['username'];
    $creds['user_password'] = $_POST['password'];
    //$creds['remember'] = false;
    $user = wp_signon( $creds, false );
    if ( is_wp_error($user) ) {
      $vr_error = $user->get_error_message();
    } else {
      if (isset($_POST['redirect']) && $_POST['redirect']) {
        wp_redirect($_POST['redirect']);
		exit();
      }
	  // if (isset($_POST['redirect']) && $_POST['redirect']) 
		// echo '<script>window.location.replace("/login/");</script>';
    }
  }
}
// shows error message

function get_vr_error() {
  global $vr_error;
  if ($vr_error) {
    $return = $vr_error;
    $vr_error = false;
    return $return;
  } else {
    return false;
  }
}
// shows login form (or a message, if user already logged in)
function get_vr_login_form($redirect) {
  if (!is_user_logged_in()) {
    $return = "<form action=\"\" method=\"post\" class=\"vr_form vr_login\">\r\n";
    $error = get_vr_error();
    if ($error)
      $return .= "<p class=\"error\">{$error}</p>\r\n";
    $return .= "  <p>
      <label for=\"vr_username\">".__('Username','theme')."</label>
      <input type=\"text\" id=\"vr_username\" name=\"username\" value=\"".(isset($_POST['username'])?$_POST['username']:"")."\"/>
    </p>\r\n";
    $return .= "  <p>
      <label for=\"vr_password\">".__('Password','theme')."</label>
      <input type=\"password\" id=\"vr_password\" name=\"password\"/>
    </p>\r\n";
    if ($redirect)
      $return .= "<input type=\"hidden\" name=\"redirect\" value=\"{$redirect}\">\r\n";
    $return .= "<input type=\"submit\" value=".__('Login','theme')." />\r\n";
    $return .= "</form>\r\n";
  } else {
    $return = "<p><a href=\"".wp_logout_url(home_url('/login/'))."\">".__('User is already logged in','theme')."</a></p>";
  }
  return $return;
}

// adds a handy [vr_login] shortcode to use in posts/pages
add_shortcode('vr_login','vr_login_shortcode');
function vr_login_shortcode ($atts,$content=false) {
  $atts = shortcode_atts(array(
    'redirect' => home_url()
  ), $atts);
  return get_vr_login_form($atts['redirect']);
}
?>