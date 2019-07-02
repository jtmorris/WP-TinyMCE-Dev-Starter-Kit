<?php
/**
 * Plugin Name: TinyMCE Developer Starter Kit
 * Plugin URI: https://web.archive.org/web/20160815193906/http://johnmorris.me/computers/software/how-to-create-a-tinymce-editor-dialog-window-in-a-wordpress-plugin/
 * Description: A simple starting point for developers to practice TinyMCE and WordPress integration.
 * Version: 1.0
 * Author: John Morris
 * Author URI: 
 * License: GPL2
 */

/*  Copyright 2014-2015 John Morris  (email : johntylermorris@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


//////////////////////////////////
/// Some Notes Before We Begin ///
//////////////////////////////////
/**
 * 1.)  This starter kit is based on the tutorial on my website. The tutorial can
 * be found at https://web.archive.org/web/20160815193906/http://johnmorris.me/computers/software/how-to-create-a-tinymce-editor-dialog-window-in-a-wordpress-plugin/
 *
 * 2.)  In keeping with WordPress plugin best practices
 * (http://code.tutsplus.com/articles/tips-for-best-practices-in-wordpress-development--cms-20649),
 * everything will be given a unique prefix to prevent plugin naming collisions.
 * This prefix will be "TDSK" which is the acronym of the plugin name.  The case
 * of this prefix will conform with WordPress coding standards and PHP best practices.
 *
 * 3.)  This plugin will be kept to as few files as possible and use functional
 * programming instead of object oriented programming to keep things simple.  In
 * practice, splitting code into multiple files and embracing some object oriented
 * approaches can be helpful and vastly improve readability.
 */



////////////////////////
/// Define Constants ///
////////////////////////
/**
 * Defining these constants is a habit I developed to smooth over some confusing
 * aspects of WordPress.  Many of these are simply aliases to WordPress functions
 * and could, theoretically, be ommitted.  But these make more sense to me than
 * the function names and parameters, so I like them.
 */

//  Absolute path to plugin's root directory in file system.
define ( 'TDSK_ROOT_PATH', plugin_dir_path( __FILE__ ) );

//  URL to the plugin's root directory.
define( 'TDSK_ROOT_URL', plugin_dir_url( __FILE__ ) );

//  Absolute path to the main plugin file (this one).
define( 'TDSK_PLUGIN_FILE', TDSK_ROOT_PATH . 'tinymce-dev-starter.php' );



////////////////////////
/// Setup Shortcodes ///
////////////////////////
/**
 * Some of the usages for this starter kit will depend on there being a shortcode
 * defined.  We could use a built-in shortcode, but I think it's more instructive
 * to create our own.
 *
 * For information on shortcodes, visit the following links:
 * - http://codex.wordpress.org/Shortcode_API
 * - http://planetozh.com/blog/2008/03/wordpress-25-shortcodes-api-overview/
 * - http://aaron.jorb.in/blog/2010/02/wordpress-shortcodes-a-how-to-by-example/
 */

//  [tdsk-dumb-shotcode text="___________"]: Outputs the text attribute text in red.
add_shortcode( 'tdsk-dumb-shortcode', 'tdsk_dumb_shortcode_func' );

/**
 * The function handler for the tdsk-dumb-shortcode shortcode.
 * @param  ARRAY_A $attributes An array of all attributes passed to the shortcode.
 * @return string              The HTML to output in place of the shortcode.
 */
function tdsk_dumb_shortcode_func( $attributes ) {
    if( !array_key_exists( 'text', $attributes ) ) {
        $text_to_display = "No text provided in shortcode.";
    }
    else {
        $text_to_display = $attributes['text'];
    }

    return '<span style="color: #900;">' . $text_to_display . '</span>';
}


///////////////////////
/// Enqueue jQuery ///
//////////////////////
/**
 * You may or may not depend on jQuery in your TinyMCE dialog. If you do, you
 * must enqueue it everywhere your dialog will be used.  For this example, we
 * need it in the admin.
 *
 * More information on jQuery and TinyMCE in Wordpress can be found here:
 * https://web.archive.org/web/20160518130829/http://johnmorris.me/computers/using-jquery-and-jquery-ui-in-tinymce-dialog-iframe/
 */
add_action( 'admin_enqueue_scripts', 'tdsk_enqueue_admin_scripts' );
function tdsk_enqueue_admin_scripts() {
    wp_enqueue_script( 'jquery' );
}




////////////////////////////////
/// Hook into TinyMCE Editor ///
////////////////////////////////
/**
 * Here is where the good stuff starts. This is where we tell WordPress that the
 * TinyMCE instances should expect a new button on the toolbar, and that we will
 * be defining a TinyMCE "plugin" which takes care of this button.
 */

//  Step 1: Register a toolbar button
//  Documentation: http://codex.wordpress.org/Plugin_API/Filter_Reference/mce_buttons,_mce_buttons_2,_mce_buttons_3,_mce_buttons_4
add_filter( 'mce_buttons', 'tdsk_register_tinymce_button' );

/**
 * The function that tells WordPress to tell TinyMCE it has a new button.
 * @param  ARRAY_N $button_array Array of button IDs for the toolbar.
 * @return ARRAY_N               The button array with the new button added.
 */
function tdsk_register_tinymce_button( $button_array ) {
    /**
     * Our goal is to insert the ID of a button we will define in TinyMCE's initialization
     * code.  This button definition is found in the tinymce-plugin.js file.  We
     * insert the ID in the position we want the button on the toolbar.  I'm going
     * to add it to the end of the toolbar.
     *
     * But, we should take into account when we want this button to appear.
     * In most cases, we only want our TinyMCE addition to work in the WordPress
     * admin section, not in any editors that may or may not be publicly
     * available (some plugins provide user facing TinyMCE editors).  If this is
     * true, we need to do some WordPress footwork to find where we are and if
     * we should show the button.
     *
     * For this example, I will illustrate limiting to showing the button on
     * post and page editors in the admin.
     */

    global $current_screen; //  WordPress contextual information about where we are.

    $type = $current_screen->post_type;

    if( is_admin() && ( $type == 'post' || $type == 'page' ) ) {
        //  Okay, our conditions for showing the button have been met. Therefore,
        //  we need to tack on the new button ID to the button array.  This ID
        //  must match the one in our button definition in tinymce-plugin.js
        array_push( $button_array, 'tdsk_button' );
    }

    return $button_array;
}



//  Step 2: Tell WordPress to tell TinyMCE we will be defining a plugin that deals with our button.
//  Documentation: http://codex.wordpress.org/Plugin_API/Filter_Reference/mce_external_plugins
add_filter( 'mce_external_plugins', 'tdsk_register_tinymce_plugin' );

/**
 * The function that tells WordPress to tell TinyMCE it has a new plugin.
 * @param  ARRAY_A $plugin_array Array of plugin JavaScript files.
 * @return ARRAY_A               The plugin array with the new plugin file added.
 */
function tdsk_register_tinymce_plugin( $plugin_array ) {
    /**
     * Much like with the button, our goal is to tack something onto the plugin
     * array.  In this case, it is the path to the JavaScript file which handles
     * our new plugin and button.
     *
     * Also, since we restricted the button to only show when in the admin and
     * editing a page or post, I'm going to do the same here and only register
     * the plugin if in the same situation.
     */

    global $current_screen; //  WordPress contextual information about where we are.

    $type = $current_screen->post_type;

    if( is_admin() && ( $type == 'post' || $type == 'page' ) ) {
        //  Okay, our conditions for registering the plugin have been met. Therefore,
        //  we need to tack on the new plugin file to the plugin array.  The array
        //  key in the plugin array must match the plugin name in tinymce-plugin.js
        $plugin_array['tdsk_plugin'] = TDSK_ROOT_URL . 'tinymce-plugin.js';
    }

    return $plugin_array;
}





////////////////////////////////////
/// Save Data to Pass to TinyMCE ///
////////////////////////////////////
add_action( 'admin_head', 'tdsk_save_tinymce_data' );

/**
 * Outputs JavaScript which stores data we need to pass from the server to the
 * client.
 */
function tdsk_save_tinymce_data() {
    /**
     * Basically, this function is middle-man for PHP and JavaScript. If your
     * TinyMCE dialog needs to extract data from WordPress and PHP in some way,
     * the easiest thing to do is save it below.
     *
     * Just to prove it works, I'm going to pass the PHP version.  If you don't
     * need to share information, delete this.
     *
     * Essentially, all we are doing is creating a JavaScript variable in the
     * global namespace. We will then retrieve this in the TinyMCE dialog. This
     * function is merely outputting content the the head section of the WordPress
     * admin.
     */

     ?>
     <script type='text/javascript'>
        var tdsk_data = {
            'php_version': '<?php echo phpversion(); ?>'
        };
     </script>
     <?php
}
