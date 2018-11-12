<?php
/** 
 *  Plugin Name:    Crayon Syntax Highlighter - REST API Support
 *  Plugin URI:     https://github.com/michaeluno/rest-api-support-for-crayon-syntax-highlighter
 *  Description:    Adds WordPress REST API support for Crayon Syntax Highlighter.
 *  Author:         Michael Uno
 *  Author URI:     http://en.michaeluno.jp/
 *  Version:        1.0.0
 */

/**
 * The main routine.
 * @since   1.0.0
 */
final class CrayonSyntaxHighlighter_RESTAPISupport {

    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'replyToLoadPlugin' ) );
    }
        public function replyToLoadPlugin() {

            $this->___include();
            if ( ! $this->___shouldProceed() ) {
                return;
            }
            $this->___doMain();

        }
            /**
             * Handles inclusion of files
             */
            private function ___include() {
                include( dirname( __FILE__ ) . '/include/CrayonSyntaxHighlighter_RESTAPISupport_Utility.php' );
            }
            /**
             * Does the main routine.
             */
            private function ___doMain() {
                $_bSavePostAdded = CrayonSyntaxHighlighter_RESTAPISupport_Utility::hasCallback( 'save_post', 'CrayonWP::save_post' );
                if ( ! $_bSavePostAdded ) {
                    add_action('update_post', 'CrayonWP::save_post', 10, 2);
                    add_action('save_post', 'CrayonWP::save_post', 10, 2);
                    add_filter('wp_insert_post_data', 'CrayonWP::filter_post_data', '99', 2);
                }
            }

            /**
             * Checks if the main routine shouold be performed.
             * @return bool
             */
            private function ___shouldProceed() {
                if ( ! CrayonSyntaxHighlighter_RESTAPISupport_Utility::isREST() ) {
                    return false;
                }

                // Crayon Syntax Highlighter is not loaded.
                if ( ! class_exists( 'CrayonWP' ) ) {
                    return false;
                }

                return true;
            }

}
new CrayonSyntaxHighlighter_RESTAPISupport;

