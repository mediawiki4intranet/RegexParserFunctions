<?php

namespace MediaWiki\Extension\RegexParserFunctions;

use Parser;

class Hooks {

    /**
     * Sets up parser functions
     *
     * @param Parser $parser
     * @return bool
     */
    public static function onParserFirstCallInit( Parser $parser ): bool {
        $parser->setFunctionHook( 'regex', [ self::class, 'regexParserFunction' ] );
        $parser->setFunctionHook( 'regexp', [ self::class, 'regexParserFunction' ] );
        $parser->setFunctionHook( 'urlencode', [ self::class, 'urlencodeParserFunction' ] );
        $parser->setFunctionHook( 'eval', [ self::class, 'evalParserFunction' ] );
        
        return true;
    }

    /**
     * Performs regular expression search or replacement.
     *
     * @param Parser $parser Instance of running Parser.
     * @param string|null $subject Input string to evaluate.
     * @param string|null $pattern Regular expression pattern.
     * @param string|null $replacement Regular expression replacement.
     * @return string Result of replacing pattern with replacement in string, or matching text.
     */
    public static function regexParserFunction( Parser $parser, ?string $subject = null, ?string $pattern = null, ?string $replacement = null ): string {
        if ( $subject === null || $pattern === null ) {
            return '';
        }

        $acceptable = '/^([\\/\\|%])[^\0]*\\1[imsu]*$/s';
        if ( !preg_match( $acceptable, $pattern ) ) {
            // wfMsg() удалена в новых версиях MW, используем wfMessage()->text()
            return wfMessage( 'regexp-unacceptable', $pattern )->text();
        }

        if ( $replacement === null ) {
            return preg_match( $pattern, $subject, $matches ) ? $matches[0] : '';
        } else {
            return (string)preg_replace( $pattern, $replacement, $subject );
        }
    }

    /**
     * URL encodes a string.
     * Note: MediaWiki core already provides {{urlencode:}} since version 1.17.
     * Keeping this for backward compatibility.
     *
     * @param Parser $parser
     * @param string $value
     * @return string
     */
    public static function urlencodeParserFunction( Parser $parser, string $value = '' ): string {
        return urlencode( $value );
    }

    /**
     * Evaluates a template string.
     * * @param Parser $parser
     * @param string ...$args Variadic arguments (PHP 5.6+) replace func_get_args()
     * @return string
     */
    public static function evalParserFunction( Parser $parser, string ...$args ): string {
        if ( empty( $args ) ) {
            return '';
        }
        
        $templateCall = '{{' . implode( '|', $args ) . '}}';
        return $parser->replaceVariables( $templateCall );
    }
}