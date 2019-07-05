<?php
namespace Yiisoft\Mailer\Tests;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Mailer\Composer;
use Yiisoft\View\{Theme, View};

class ComposerTest extends TestCase
{
    /**
     * @return Composer $composer instance.
     */
    private function getComposer()
    {
        return $this->get(Composer::class);
    }

    /**
     * @dataProvider setUpData
     */
    public function testSetup($viewPath, $htmlLayout, $textLayout)
    {
        $composer = new Composer($this->get(View::class), $viewPath);
        $composer->setHtmlLayout($htmlLayout);
        $composer->setTextayout($textLayout);
        $this->assertEquals($composer->getView(), $this->get(View::class));
        $this->assertSame($viewPath, $composer->getViewPath());
        $this->assertSame($htmlLayout, $this->getObjectPropertyValue($composer, 'htmlLayout'));
        $this->assertSame($textLayout, $this->getObjectPropertyValue($composer, 'textLayout'));
    }

    public function setUpData()
    {
        return [
            ['/tmp/views', '', ''],
            ['/tmp/views', 'layouts/html', 'layouts/text'],
        ];
    }

    public function testSetView()
    { 
        $view = new View('/tmp/views', new Theme(), $this->get(EventDispatcherInterface::class), $this->get(LoggerInterface::class));
        $composer = $this->getComposer();
        $composer->setView($view);

        $this->assertEquals($composer->getView(), $view);
    }

    public function testSetViewPath()
    {
        $path = '/tmp/views';
        $composer = $this->getComposer();
        $composer->setViewPath($path);
        $this->assertEquals($composer->getViewPath(), $path);
    }

    public function testCreateTemplate()
    {
        $composer = $this->getComposer();
        $method = new \ReflectionMethod(Composer::class, 'createTemplate');
        $method->setAccessible(true);
        
        $viewName = 'test-view';
        /* @var $template Template */
        $template = $method->invoke($composer, $viewName);

        $this->assertSame($composer->getView(), $this->getObjectPropertyValue($template, 'view'));
        $this->assertEquals($viewName, $this->getObjectPropertyValue($template, 'viewName'));
        $this->assertEquals($this->getObjectPropertyValue($composer, 'viewPath'), $template->getViewPath());
        $this->assertEquals($this->getObjectPropertyValue($composer, 'htmlLayout'), $this->getObjectPropertyValue($template, 'htmlLayout'));
        $this->assertEquals($this->getObjectPropertyValue($composer, 'textLayout'), $this->getObjectPropertyValue($template, 'textLayout'));
    }
}