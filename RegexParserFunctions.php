<?php
/*
 * RegexParserFunctions.php - Allows regular expression search and replace within a string
 * @author Jim R. Wilson, Vitaliy Filippov
 * @version 2015-10-22
 * @copyright Copyright (c) 2007 Jim R. Wilson, (c) 2011+ Vitaliy Filippov
 * @license The MIT License - http://www.opensource.org/licenses/mit-license.php
 * -----------------------------------------------------------------------
 * Description:
 *     This is a MediaWiki extension which adds a parser function for performing
 *     regular expression searches and replacements.
 * Requirements:
 *     MediaWiki 1.16.x or higher
 * Installation:
 *     1. Drop this script (RegexParserFunctions.php) in $IP/extensions
 *         Note: $IP is your MediaWiki install dir.
 *     2. Enable the extension by adding this line to your LocalSettings.php:
 *         require_once('extensions/RegexParserFunctions.php');
 * -----------------------------------------------------------------------
 * Copyright (c) 2007 Jim R. Wilson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 * -----------------------------------------------------------------------
 */

# Confirm MW environment
if (!defined('MEDIAWIKI'))
    die("Not in MediaWiki environment");

# Credits
$wgExtensionCredits['parserhook'][] = array(
    'name'          => 'RegexParserFunctions',
    'author'        => 'Jim R. Wilson (wilson.jim.r<at>gmail.com), Vitaliy Filippov (vitalif<at>mail.ru)',
    'url'           => 'http://wiki.4intra.net/RegexParserFunctions',
    'description'   => 'Adds a parser function for search and replace using regular expressions.',
    'version'       => '2015-12-02',
);

$wgHooks['LanguageGetMagic'][] = 'RegexParserFunctions::getMagic';
$wgHooks['ParserFirstCallInit'][] = 'RegexParserFunctions::initParser';
$wgExtensionMessagesFiles['RegexParserFunctions'] = dirname(__FILE__).'/RegexParserFunctions.i18n.php';

/**
 * Wrapper class for encapsulating Regexp related parser methods
 */
class RegexParserFunctions
{
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
        $acceptable = '/^([\\/\\|%])[^\0]*\\1[imsu]*$/s';
        if ( !preg_match( $acceptable, $pattern ) ) {
            return wfMsg( 'regexp-unacceptable', $pattern );
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
