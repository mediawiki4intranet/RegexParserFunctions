<?php

class RegexParserFunctions {
 
    /**
     * Performs regular expression search or replacement.
     *
     * @param Parser $parser Instance of running Parser.
     * @param String $subject Input string to evaluate.
     * @param String $pattern Regular expression pattern - must use /, | or % delimiter
     * @param String $replacement Regular expression replacement.
     * @return String Result of replacing pattern with replacement in string, or matching text if replacement was omitted.
     */
    static function regexParserFunction( $parser, $subject = null, $pattern = null, $replacement = null ) {
        if ( $subject === null || $pattern === null) {
            return '';
        }
        if ( preg_match( $pattern, null ) === false ) {
            return wfMessage( 'regexp-unacceptable', $pattern );
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

    /**
     * Adds magic words for parser functions
     * @param Array $magicWords
     * @param $langCode
     * @return Boolean Always true
     */
    static function getMagic( &$magicWords, $langCode ) {
        $magicWords['regex'] = array( 0, 'regex' );
        $magicWords['regexp'] = array( 0, 'regexp' );
        $magicWords['urlencode'] = array( 0, 'urlencode' );
        $magicWords['eval'] = array( 0, 'eval' );
        return true;
    }

    /**
     * Sets up parser functions
     */
    static function initParser( $parser ) {
        $parser->setFunctionHook( 'regex', __CLASS__.'::regexParserFunction' );
        $parser->setFunctionHook( 'regexp', __CLASS__.'::regexParserFunction' );
        $parser->setFunctionHook( 'urlencode', __CLASS__.'::urlencodeParserFunction' );
        $parser->setFunctionHook( 'eval', __CLASS__.'::evalParserFunction' );
        return true;
    }
}