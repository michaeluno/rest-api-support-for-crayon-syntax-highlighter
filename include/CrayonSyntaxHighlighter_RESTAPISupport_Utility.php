<?php
/**
 * Crayon Syntax Highlighter - REST API Support
 *
 * Adds WordPress REST API support for Crayon Syntax Highlighter.
 *
 * @copyright   Michael Uno 2018 <http://en.michaeluno.jp/>
 */

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
     * @param   string    $sHookName      The action/filter hook name
     * @param   callable  $cCallable
     * @param   int       $iPriority
     * @return  bool
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