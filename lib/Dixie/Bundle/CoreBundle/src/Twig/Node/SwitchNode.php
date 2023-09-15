<?php

declare(strict_types=1);

namespace Talav\CoreBundle\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Class SwitchNode
 * Based on the rejected Twig pull request: https://github.com/fabpot/Twig/pull/185.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 *
 * @since 3.0
 */
final class SwitchNode extends Node
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('switch (')
            ->subcompile($this->getNode('value'))
            ->raw(") {\n")
            ->indent();
        /** @psalm-var Node[] $cases */
        $cases = $this->getNode('cases'); // @phpstan-ignore-line
        foreach ($cases as $case) {
            if (!$case->hasNode('body')) {
                continue;
            }
            /** @psalm-var Node[] $values */
            $values = $case->getNode('values');
            foreach ($values as $value) {
                $compiler->write('case ')
                    ->subcompile($value)
                    ->raw(":\n");
            }
            $compiler
                ->write("{\n")
                ->indent()
                ->subcompile($case->getNode('body'))
                ->write("break;\n")
                ->outdent()
                ->write("}\n");
        }
        if ($this->hasNode('default')) {
            $compiler->write("default:\n")
                ->write("{\n")
                ->indent()
                ->subcompile($this->getNode('default'))
                ->outdent()
                ->write("}\n");
        }
        $compiler->outdent()
            ->write("}\n");
    }
}
