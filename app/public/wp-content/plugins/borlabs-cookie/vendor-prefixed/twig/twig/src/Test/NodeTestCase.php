<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by borlabs on 06-February-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace Borlabs\Cookie\Dependencies\Twig\Test;

use PHPUnit\Framework\TestCase;
use Borlabs\Cookie\Dependencies\Twig\Compiler;
use Borlabs\Cookie\Dependencies\Twig\Environment;
use Borlabs\Cookie\Dependencies\Twig\Loader\ArrayLoader;
use Borlabs\Cookie\Dependencies\Twig\Node\Node;

abstract class NodeTestCase extends TestCase
{
    abstract public function getTests();

    /**
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null, $isPattern = false)
    {
        $this->assertNodeCompilation($source, $node, $environment, $isPattern);
    }

    public function assertNodeCompilation($source, Node $node, Environment $environment = null, $isPattern = false)
    {
        $compiler = $this->getCompiler($environment);
        $compiler->compile($node);

        if ($isPattern) {
            $this->assertStringMatchesFormat($source, trim($compiler->getSource()));
        } else {
            $this->assertEquals($source, trim($compiler->getSource()));
        }
    }

    protected function getCompiler(Environment $environment = null)
    {
        return new Compiler($environment ?? $this->getEnvironment());
    }

    protected function getEnvironment()
    {
        return new Environment(new ArrayLoader([]));
    }

    protected function getVariableGetter($name, $line = false)
    {
        $line = $line > 0 ? "// line $line\n" : '';

        return sprintf('%s($context["%s"] ?? null)', $line, $name);
    }

    protected function getAttributeGetter()
    {
        return 'borlabs_twig_get_attribute($this->env, $this->source, ';
    }
}
