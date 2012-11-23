<?php

namespace Rithis\PageBundle\Features\Context;

use Behat\Symfony2Extension\Context\KernelDictionary,
    Behat\Gherkin\Node\PyStringNode,
    Behat\Behat\Context\BehatContext;

use PHPUnit_Framework_Assert as Assert;

use Twig_Loader_Array,
    Twig_Loader_Chain,
    Twig_Environment;

class TwigContext extends BehatContext
{
    use KernelDictionary;

    /**
     * @var \Twig_Loader_Array
     */
    private $twigLoader;

    /**
     * @var string
     */
    private $templateOutput;

    /**
     * @BeforeScenario
     */
    public function injectArrayLoader()
    {
        if (!$this->twigLoader) {
            $this->twigLoader = new Twig_Loader_Array([]);

            $twig = $this->getTwig();
            $twig->setLoader(new Twig_Loader_Chain([
                $this->twigLoader,
                $twig->getLoader()
            ]));
        }
    }

    /**
     * @Given /^шаблон "([^"]*)" со следующим содержанием:$/
     */
    public function saveTemplate($name, PyStringNode $content)
    {
        $this->twigLoader->setTemplate($name, $content->getRaw());
    }

    /**
     * @When /^я вывел шаблон "([^"]*)"$/
     */
    public function renderTemplate($name)
    {
        $this->templateOutput = $this->getTwig()->render($name);
    }

    /**
     * @Then /^вывод шаблона должен содержать:$/
     */
    public function templateOutputMustContain(PyStringNode $string)
    {
        Assert::assertContains($string->getRaw(), $this->templateOutput, "Вхождения нет");
    }

    /**
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->getContainer()->get('twig');
    }
}
