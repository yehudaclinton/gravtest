<?php

/**
 * @package   Gantry5
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2021 RocketTheme, LLC
 * @license   Dual License: MIT or GNU/GPLv2 and later
 *
 * http://opensource.org/licenses/MIT
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Gantry Framework code that extends GPL code is considered GNU/GPLv2 and later
 */

namespace Gantry\Component\Twig\TokenParser;

use Gantry\Component\Twig\Node\TwigNodeMarkdown;
use Twig\Error\SyntaxError;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Adds ability to inline markdown between tags.
 *
 * {% markdown %}
 * This is **bold** and this _underlined_
 *
 * 1. This is a bullet list
 * 2. This is another item in that same list
 * {% endmarkdown %}
 */
class TokenParserMarkdown extends AbstractTokenParser
{
    /**
     * @param Token $token
     * @return TwigNodeMarkdown
     * @throws SyntaxError
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideMarkdownEnd'], true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        return new TwigNodeMarkdown($body, $lineno, $this->getTag());
    }
    /**
     * Decide if current token marks end of Markdown block.
     *
     * @param Token $token
     * @return bool
     */
    public function decideMarkdownEnd(Token $token)
    {
        return $token->test('endmarkdown');
    }
    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'markdown';
    }
}
