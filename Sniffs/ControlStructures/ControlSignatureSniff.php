<?php
/**
 * Verifies that control statements conform to their coding standards.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Denis Roussel <denis.roussel@gmail.com>
 * @author   Morgan Brunot <brunot.morgan@gmail.com>
 * @license  http://spdx.org/licenses/BSD-3-Clause BSD 3-clause "New" or "Revised" License
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * Verifies that control statements conform to their coding standards.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Denis Roussel <denis.roussel@gmail.com>
 * @author   Morgan Brunot <brunot.morgan@gmail.com>
 * @license  http://spdx.org/licenses/BSD-3-Clause BSD 3-clause "New" or "Revised" License
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class C2iS_Sniffs_ControlStructures_ControlSignatureSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'PHP',
    );


    /**
     * Returns the patterns that this test wishes to verify.
     *
     * @return array(string)
     */
    protected function getPatterns()
    {
        return array(
          'tryEOL',
          '{EOL...}',
          'catch (...)EOL',
          'doEOL',
          'while (...);EOL',
          'while (...)EOL',
          'for (...)EOL',
          'if (...)EOL',
          'foreach (...)EOL',
          '}EOLelse if (...)EOL',
          '}EOLelseif (...)EOL',
          '}EOLelseEOL',
        );
    }//end getPatterns()


}//end class

?>