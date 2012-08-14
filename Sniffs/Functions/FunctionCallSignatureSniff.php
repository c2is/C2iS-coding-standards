<?php
/**
 * C2iS_Sniffs_Functions_FunctionCallSignatureSniff.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @author    Denis Roussel <denis.roussel@gmail.com>
 * @copyright 2006-2011 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.3.6
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class C2iS_Sniffs_Functions_FunctionCallSignatureSniff extends PEAR_Sniffs_Functions_FunctionCallSignatureSniff
{

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Find the next non-empty token.
        $openBracket = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call.
            return;
        }

        if (isset($tokens[$openBracket]['parenthesis_closer']) === false) {
            // Not a function call.
            return;
        }

        // Find the previous non-empty token.
        $search   = PHP_CodeSniffer_Tokens::$emptyTokens;
        $search[] = T_BITWISE_AND;
        $previous = $phpcsFile->findPrevious($search, ($stackPtr - 1), null, true);
        if ($tokens[$previous]['code'] === T_FUNCTION) {
            // It's a function definition, not a function call.
            return;
        }

        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];

        if (($stackPtr + 1) !== $openBracket) {
            // Checking this: $value = my_function[*](...).
            $error = 'Space before opening parenthesis of function call prohibited';
            $phpcsFile->addError($error, $stackPtr, 'SpaceBeforeOpenBracket');
        }

        // TODO: improve this part to validate EOL after brackets
        /*
        $next = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), null, true);
        if ($tokens[$next]['code'] === T_SEMICOLON) {
            if (in_array($tokens[($closeBracket + 1)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
                $error = 'toto';
                $phpcsFile->addError($error, $closeBracket, 'SpaceAfterCloseBracket');
            }
        }
        */

        // Check if this is a single line or multi-line function call.
        if ($tokens[$openBracket]['line'] === $tokens[$closeBracket]['line']) {
            $this->processSingleLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        } else {
            $this->processMultiLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
        }

    }//end process()

    /**
     * Processes multi-line calls.
     *
     * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
     * @param int                  $stackPtr    The position of the current token
     *                                          in the stack passed in $tokens.
     * @param int                  $openBracket The position of the openning bracket
     *                                          in the stack passed in $tokens.
     * @param array                $tokens      The stack of tokens that make up
     *                                          the file.
     *
     * @return void
     */
    public function processMultiLineCall(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens)
    {
        // We need to work out how far indented the function
        // call itself is, so we can work out how far to
        // indent the arguments.
        $functionIndent = 0;
        for ($i = ($stackPtr - 1); $i >= 0; $i--) {
            if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
                $i++;
                break;
            }
        }

        if ($tokens[$i]['code'] === T_WHITESPACE) {
            $functionIndent = strlen($tokens[$i]['content']);
        }

        // Each line between the parenthesis should be indented n spaces.
        $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        $lastLine     = $tokens[$openBracket]['line'];
        for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
            // Skip nested function calls.
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $i        = $tokens[$i]['parenthesis_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            if ($tokens[$i]['line'] !== $lastLine) {
                $lastLine = $tokens[$i]['line'];

                // Ignore heredoc indentation.
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$heredocTokens) === true) {
                    continue;
                }

                // Ignore multi-line string indentation.
                if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens) === true) {
                    if ($tokens[$i]['code'] === $tokens[($i - 1)]['code']) {
                        continue;
                    }
                }

                // We changed lines, so this should be a whitespace indent token, but first make
                // sure it isn't a blank line because we don't need to check indent unless there
                // is actually some code to indent.
                if ($tokens[$i]['code'] === T_WHITESPACE) {
                    $nextCode = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), ($closeBracket + 1), true);
                    if ($tokens[$nextCode]['line'] !== $lastLine) {
                        $error = 'Empty lines are not allowed in multi-line function calls';
                        $phpcsFile->addError($error, $i, 'EmptyLine');
                        continue;
                    }
                } else {
                    $nextCode = $i;
                }

                // Check if the next line contains an object operator, if so rely on
                // the ObjectOperatorIndentSniff to test the indent.
                if ($tokens[$nextCode]['type'] === 'T_OBJECT_OPERATOR') {
                    continue;
                }

                if ($nextCode === $closeBracket) {
                    // Closing brace needs to be indented to the same level
                    // as the function call.
                    $expectedIndent = $functionIndent;
                } else {
                    $expectedIndent = ($functionIndent + $this->indent);
                }

                if ($tokens[$i]['code'] !== T_WHITESPACE) {
                    $foundIndent = 0;
                } else {
                    $foundIndent = strlen($tokens[$i]['content']);
                }

                if ($expectedIndent !== $foundIndent) {
                    $error = 'Multi-line function call not indented correctly; expected %s spaces but found %s';
                    $data  = array(
                              $expectedIndent,
                              $foundIndent,
                             );
                    $phpcsFile->addError($error, $i, 'Indent', $data);
                }
            }//end if

            // Skip the rest of a closure.
            if ($tokens[$i]['code'] === T_CLOSURE) {
                $i        = $tokens[$i]['scope_closer'];
                $lastLine = $tokens[$i]['line'];
                continue;
            }

            if ($this->allowMultipleArguments === false && $tokens[$i]['code'] === T_COMMA) {
                // Comma has to be the last token on the line.
                $next = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), $closeBracket, true);
                if ($next !== false
                    && $tokens[$i]['line'] === $tokens[$next]['line']
                ) {
                    $error = 'Only one argument is allowed per line in a multi-line function call';
                    $phpcsFile->addError($error, $next, 'MultipleArguments');
                }
            }
        }//end for

        $prev = $phpcsFile->findPrevious(array(T_WHITESPACE, T_CLOSE_PARENTHESIS, T_CLOSE_CURLY_BRACKET), ($closeBracket - 1), null, true);
        if ($tokens[$prev]['line'] === $tokens[$closeBracket]['line']) {
            $error = 'Closing parenthesis of a multi-line function call must be on a line by itself';
            $phpcsFile->addError($error, $closeBracket, 'CloseBracketLine');
        }

    }//end processMultiLineCall()

}//end class
?>
