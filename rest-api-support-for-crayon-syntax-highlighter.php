<?php
/** 
 *  Plugin Name:    REST API Support for Crayon Syntax Highlighter
 *  Plugin URI:     https://github.com/michaeluno/rest-api-support-for-crayon-syntax-highlighter
 *  Description:    Add REST API support for Crayon Syntax Highlighter.
 *  Author:         Michael Uno
 *  Author URI:     http://en.michaeluno.jp/
 *  Version:        1.0.0
 */

class CrayonSyntaxHighlighter_RESTAPISupport {

    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'replyToLoadPlugin' ) );
    }

    public function replyToLoadPlugin() {

        if ( ! CrayonSyntaxHighlighter_RESTAPISupport_Utility::isREST() ) {
            return;
        }

        // Crayon Syntax Highlighter is not loaded.
        if ( ! class_exists( 'CrayonWP' ) ) {
            return;
        }

        $_bSavePostAdded = CrayonSyntaxHighlighter_RESTAPISupport_Utility::hasCallback( 'save_post', 'CrayonWP::save_post' );
        if ( ! $_bSavePostAdded ) {
            add_action('update_post', 'CrayonWP::save_post', 10, 2);
            add_action('save_post', 'CrayonWP::save_post', 10, 2);
            add_filter('wp_insert_post_data', 'CrayonWP::filter_post_data', '99', 2);
        }

    }

}
new CrayonSyntaxHighlighter_RESTAPISupport;

/**
 * Provides utility methods.
 * @since   1.0.0
 */
class CrayonSyntaxHighlighter_RESTAPISupport_Utility {

    /**
     * @remark  `( defined( 'REST_REQUEST' ) && REST_REQUEST )` is too late so not reliable.
     * @return bool
     */
    static public function isREST() {
        $_aURIParts = explode( '?', $_SERVER[ 'REQUEST_URI' ] );
        if ( false === strpos( $_aURIParts[ 0 ], '/wp-json/' ) ) {
            return false;
        }
        return true;
    }

    /**
     * @param $sHookName
     * @param callable $cCallable
     * @param int $iPriority
     * @return bool
     */
    static public function hasCallback( $sHookName, $cCallable, $iPriority=0 ) {
        if ( ! is_callable( $cCallable ) ) {
            return false;
        }
        $_oWPHook = self::___getWPHookObject( $sHookName );
        if ( ! isset( $_oWPHook->callbacks ) || ( ! $_oWPHook instanceof WP_Hook ) ) {
            return false;
        }
        if ( $iPriority ) {
            return self::___hasCallbackByPriority( $_oWPHook, $iPriority, $cCallable );
        }
        foreach( $_oWPHook as $_iPriority => $_aCallbacks ) {
            if ( self::___hasCallbackByPriority( $_oWPHook, $_iPriority, $cCallable ) ) {
                return true;
            }
        }
        return false;

    }
        /**
         * @param WP_Hook $oWPHook
         * @param $iPriority
         * @param callable  $cCallable
         *
         * @return bool
         */
        static private function ___hasCallbackByPriority( WP_Hook $oWPHook, $iPriority, $cCallable ) {
            if ( ! isset( $oWPHook->callbacks[ $iPriority ] ) ) {
                return false;
            }
            foreach( $oWPHook->callbacks[ $iPriority ] as $_sName => $_aCallback ) {
                if ( $_aCallback[ 'function' ] === $cCallable ) {
                    return true;
                }
            }
            return false;
        }
        /**
         * @param $sHookName
         *
         * @return bool|WP_Hook
         */
        static private function ___getWPHookObject( $sHookName ) {
            $_aWPFilters = isset( $GLOBALS[ 'wp_filter' ] )
                ? $GLOBALS[ 'wp_filter' ]
                : array();
            return isset( $_aWPFilters[ $sHookName ] )
                ? $_aWPFilters[ $sHookName ]
                : false;
        }

}