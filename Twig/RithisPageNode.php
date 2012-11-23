<?php

namespace Rithis\PageBundle\Twig;

use Twig_Compiler,
    Twig_Node;

class RithisPageNode extends Twig_Node
{
    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$page = $this->env->getExtension("rithis-page")->getPage(')
            ->subcompile($this->getNode('tags'))
            ->write(");\n");

        $compiler->write('$template = ');
        if ($this->hasNode('template')) {
            $compiler
                ->subcompile($this->getNode('template'))
                ->write(";\n");
        } else {
            $compiler
                ->write('$this->env->getExtension("rithis-page")->getDefaultTemplate();')
                ->write("\n");
        }

        $compiler
            ->write('echo $this->env->render($template, array("page" => $page));')
            ->write("\n");
    }
}
