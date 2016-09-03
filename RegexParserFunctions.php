<?php

class RegexParserFunctions {
 
    /**
     * Sets up parser functions
     */
	static function onParserFirstCallInit( &$parser ) {
        $parser->setFunctionHook( 'regex', 'RegexParserFunctions::onFunctionHook' );
        return true;
    }

    /**
     * Performs regular expression search or replacement.
     *
     * @param Parser $parser Instance of running Parser.
     * @param String $subject Input string to evaluate.
     * @param String $pattern Regular expression pattern - must use /, | or % delimiter
     * @param String $replacement Regular expression replacement.
     * @return String Result of replacing pattern with replacement in string, or matching text if replacement was omitted.
     */
    static function onFunctionHook( $parser, $subject = null, $pattern = null, $replacement = null ) {
        if ( $subject === null || $pattern === null) {
            return '';
        }
        if ( preg_match( $pattern, null ) === false ) {
            return wfMessage( 'regexp-invalid', $pattern );
        }
        if ( $replacement === null ) {
            return preg_match( $pattern, $subject, $matches ) ? $matches[0] : '';
        } else {
            return preg_replace( $pattern, $replacement, $subject );
        }
    }

    static function urlencodeParserFunction( $parser, $value = '' ) {
        return urlencode( $value );
    }

    static function evalParserFunction( $parser ) {
        $args = func_get_args();
        array_shift( $args );
        $args = '{{'.implode( '|', $args ).'}}';
        return $parser->replaceVariables( $args );
    }
}