<?php

namespace Rithis\PageBundle\Twig;

use Twig_TokenParser,
    Twig_Token;

class RithisPageTokenParser extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $nodes = [];

        $nodes['tags'] = $this->parser->getExpressionParser()->parseExpression();

        if ($this->parser->getStream()->test(Twig_Token::NAME_TYPE, 'with')) {
            $this->parser->getStream()->next();

            if ($this->parser->getStream()->test(Twig_Token::NAME_TYPE, 'template')) {
                $this->parser->getStream()->next();
                $nodes['template'] = $this->parser->getExpressionParser()->parseExpression();
            }
        }

        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

        return new RithisPageNode($nodes, array(), $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'rithis_page';
    }
}
