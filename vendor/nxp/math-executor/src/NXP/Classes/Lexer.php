<?php
/**
 * This file is part of the MathExecutor package
 *
 * (c) Alexander Kiryukhin
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace NXP\Classes;

use NXP\Classes\Token\AbstractOperator;
use NXP\Classes\Token\InterfaceOperator;
use NXP\Classes\Token\TokenComma;
use NXP\Classes\Token\TokenFunction;
use NXP\Classes\Token\TokenLeftBracket;
use NXP\Classes\Token\TokenNumber;
use NXP\Classes\Token\TokenRightBracket;
use NXP\Classes\Token\TokenStringSingleQuoted;
use NXP\Classes\Token\TokenVariable;
use NXP\Classes\Token\TokenStringDoubleQuoted;
use NXP\Exception\IncorrectBracketsException;
use NXP\Exception\IncorrectExpressionException;

/**
 * @author Alexander Kiryukhin <a.kiryukhin@mail.ru>
 */
class Lexer
{
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    public function __construct($tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @param  string $input Source string of equation
     * @return array Tokens stream
     */
    public function stringToTokensStream($input)
    {
        $matches = [];
        preg_match_all($this->tokenFactory->getTokenParserRegex(), $input, $matches);
        $tokenFactory = $this->tokenFactory;
        $tokensStream = array_map(
            function ($token) use ($tokenFactory) {
                return $tokenFactory->createToken($token);
            },
            $matches[0]
        );

        return $tokensStream;
    }

    /**
     * @param  array $tokensStream Tokens stream
     * @return array Array of tokens in revers polish notation
     * @throws IncorrectBracketsException
     */
    public function buildReversePolishNotation($tokensStream)
    {
        $output = [];
        $stack = [];

        foreach ($tokensStream as $token) {
            if ($token instanceof TokenStringDoubleQuoted) {
                $output[] = $token;
            } elseif ($token instanceof TokenStringSingleQuoted) {
                $output[] = $token;
            } elseif ($token instanceof TokenNumber) {
                $output[] = $token;
            } elseif ($token instanceof TokenVariable) {
                $output[] = $token;
            } elseif ($token instanceof TokenFunction) {
                $stack[] = $token;
            } elseif ($token instanceof AbstractOperator) {
                // While we have something on the stack
                while (($count = count($stack)) > 0
                    && (
                        // If it is a function
                        ($stack[$count - 1] instanceof TokenFunction)

                        ||
                        // Or the operator at the top of the operator stack
                        //  has (left associative and equal precedence)
                        //   or has greater precedence
                        (($stack[$count - 1] instanceof InterfaceOperator) &&
                            (
                                ($stack[$count - 1]->getAssociation() == AbstractOperator::LEFT_ASSOC &&
                                    $token->getPriority() == $stack[$count - 1]->getPriority())
                                ||
                                ($stack[$count - 1]->getPriority() > $token->getPriority())
                            )
                        )
                    )

                    // And not a left bracket
                    && (!($stack[$count - 1] instanceof TokenLeftBracket))) {
                    $output[] = array_pop($stack);
                }

                // Comma operators do nothing really, don't put them on the stack
                if (! ($token instanceof TokenComma)) {
                  $stack[] = $token;
                }
            } elseif ($token instanceof TokenLeftBracket) {
                $stack[] = $token;
            } elseif ($token instanceof TokenRightBracket) {
                while (($current = array_pop($stack)) && (!($current instanceof TokenLeftBracket))) {
                    $output[] = $current;
                }
                if (!empty($stack) && ($stack[count($stack) - 1] instanceof TokenFunction)) {
                    $output[] = array_pop($stack);
                }
            }
        }
        while (!empty($stack)) {
            $token = array_pop($stack);
            if ($token instanceof TokenLeftBracket || $token instanceof TokenRightBracket) {
                throw new IncorrectBracketsException();
            }
            $output[] = $token;
        }

        return $output;
    }
}
