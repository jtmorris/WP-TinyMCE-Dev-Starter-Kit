(function($) {
    /**
     * This is an Immediately Invoked Function Expression (IIFE):
     * http://benalman.com/news/2010/11/immediately-invoked-function-expression/
     *
     * It's purpose is to isolate the code inside from the outside world. Effectively,
     * this makes everything we do here private and it gets thrown away when we're finished by preventing
     * us from attaching to the global namespace: http://stackoverflow.com/a/13352212/2523144
     *
     * In order to access anything from the big bad outside world, it must be
     * included as a parameter to the IIFE, or be part of the global namespace.
     * In this case, as some examples depend on it, we are passing in jQuery.
     *
     * I learned jQuery using the $ variable, and prefer it.  However, WordPress
     * doesn't use the $ by default since other libraries might get upset.  Since
     * I'm using only jQuery, and this is isolated from the outside world by the
     * IIFE, I like to put it back to the dollar sign during passing.  Thus,
     * the jQuery variable get's rebranded as $.
     */

    //  Step 1:  Extract data stored in the global namespace in tinymce-dev-starter.php.
    var passed_data = tdsk_data;
    var php_version = passed_data.php_version;



    //  Step 2:  Define the TinyMCE plugin and setup the button.
    //  The last property in the first tinymce.create paramenter below must be the same
    //  as the plugin you defined in tinymce-dev-starter.php. In this case, it is
    //  tdsk_plugin.  If we called it my_cool_plugin, the first parameter would change
    //  to 'tinymce.plugins.my_cool_plugin'.
    tinymce.create('tinymce.plugins.tdsk_plugin', {
        init: function(editor, url) {   //  Documentation: http://www.tinymce.com/wiki.php/API3:method.tinymce.Plugin.init
            /**
             * The editor parameter contains the TinyMCE editor instance.  The url
             * parameter contains the absolute url to the directory containing the
             * TinyMCE plugin file (this file's directory).
             *
             * We will be using editor to talk to the TinyMCE instance.  And we
             * will be using url to tell TinyMCE where files are (e.g. button
             * images).
             */

            //  Step 2a: Specify button properties and commands.
            //  The first parameter of editor.addButton must be the button ID
            //  given in tinymce-dev-starter.php. In this case, it is tdsk_button.
            editor.addButton('tdsk_button', {
                title: "TDSK's Dumb Shortcode Creator",//    Tooltip when hovering over button.
                image: url + '/tinymce-button.png',    //    The image for the button.
                cmd: 'tdsk_command'                    //    The editor command to execute on button click.
            });

            //  Step 2b: Define the "command" executed on button click.
            editor.addCommand('tdsk_command', function() {
                /**
                 * Stuff in here happens when button is clicked. Here, we want to open
                 * a dialog window, but there are plenty of other possibilities
                 * for this space.
                 */

                editor.windowManager.open(  //  Documentation: http://www.tinymce.com/wiki.php/API3:method.tinymce.WindowManager.open
                    //  Properties of the window.
                    {
                        title: "TDSK's Dumb Shortcode Creator",   //    The title of the dialog window.
                        file:  url + '/tinymce-dialog.html',      //    The HTML file with the dialog contents.
                        width: 500,                               //    The width of the dialog
                        height: 600,                              //    The height of the dialog
                        inline: 1                                 //    Whether to use modal dialog instead of separate browser window.
                    },

                    //  Parameters and arguments we want available to the window.
                    {
                        editor: editor,                   //    This is a reference to the current editor. We'll need this to insert the shortcode we create.

                        jquery: $,                        //    If you want jQuery in the dialog, you must pass it here.
                                                          //    Read More: http://johnmorris.me/computers/using-jquery-and-jquery-ui-in-tinymce-dialog-iframe/

                        php_version: php_version          //    The PHP version data we've been passing along since tinymce-dev-starter.php.
                    }
                );
            });
        }
    });


    //  Step 3:  Add the plugin to TinyMCE
    //  Documentation: http://www.tinymce.com/wiki.php/api4:method.tinymce.AddOnManager.add
    tinymce.PluginManager.add('tdsk_plugin', tinymce.plugins.tdsk_plugin);
})(jQuery);
