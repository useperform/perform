<?php

namespace Perform\DevBundle\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Perform\DevBundle\File\FileCreator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class FileCreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $fs;
    protected $twig;
    protected $creator;
    protected $input;
    protected $output;
    protected $helperSet;

    public function setUp()
    {
        $this->fs = $this->getMock(Filesystem::class);
        $this->twig = $this->getMockBuilder(\Twig_Environment::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $this->creator = new FileCreator($this->fs, $this->twig);
        $this->output = new StreamOutput(fopen('php://memory', 'w', false));
        $this->helperSet = new HelperSet([new QuestionHelper()]);
        $def = new InputDefinition([
            new InputOption('skip-existing', 's', InputOption::VALUE_NONE),
            new InputOption('force', 'f', InputOption::VALUE_NONE)
        ]);
        $this->input = new ArrayInput([], $def);
        $this->creator->setConsoleEnvironment($this->input, $this->output, $this->helperSet);
    }

    public function testForceCreate()
    {
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with('/path/to/file.txt', 'contents');

        $this->creator->forceCreate('/path/to/file.txt', 'contents');
    }

    public function testCreate()
    {
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with('/path/to/file.txt', 'contents');

        $this->creator->create('/path/to/file.txt', 'contents');
    }

    public function testCreateSkipsExisting()
    {
        $this->fs->expects($this->any())
            ->method('exists')
            ->with('/path/to/file.txt')
            ->will($this->returnValue(true));
        $this->fs->expects($this->never())
            ->method('dumpFile');
        $this->input->setOption('skip-existing', true);

        $this->creator->create('/path/to/file.txt', 'contents');
    }

    public function testCreateWithForce()
    {
        $this->fs->expects($this->any())
            ->method('exists')
            ->with('/path/to/file.txt')
            ->will($this->returnValue(true));
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with('/path/to/file.txt', 'contents');
        $this->input->setOption('force', true);

        $this->creator->create('/path/to/file.txt', 'contents');
    }

    public function testCreateConfirmNo()
    {
        $this->fs->expects($this->any())
            ->method('exists')
            ->with('/path/to/file.txt')
            ->will($this->returnValue(true));
        $this->fs->expects($this->never())
            ->method('dumpFile');
        $stream = fopen('php://memory', 'w+', false);
        fwrite($stream, 'n'.PHP_EOL);
        rewind($stream);
        $this->input->setStream($stream);

        $this->creator->create('/path/to/file.txt', 'contents');
    }

    public function testCreateConfirmYes()
    {
        $this->fs->expects($this->any())
            ->method('exists')
            ->with('/path/to/file.txt')
            ->will($this->returnValue(true));
        $this->fs->expects($this->once())
            ->method('dumpFile')
            ->with('/path/to/file.txt', 'contents');
        $stream = fopen('php://memory', 'w+', false);
        fwrite($stream, 'y'.PHP_EOL);
        rewind($stream);
        $this->input->setStream($stream);

        $this->creator->create('/path/to/file.txt', 'contents');
    }

    public function testRender()
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with('PerformDevBundle:skeletons:template.twig', ['foo' => 'bar'])
            ->will($this->returnValue('rendered'));

        $this->assertSame('rendered', $this->creator->render('template.twig', ['foo' => 'bar']));
    }

    public function resolveBundleProvider()
    {
        return [
            ['Service\\SomeService'],
            ['\\Service\\SomeService'],
            ['Service\\SomeService\\'],
            ['\\Service\\SomeService\\'],
        ];
    }

    /**
     * @dataProvider resolveBundleProvider
     */
    public function testResolveBundleClass($relativeClass)
    {
        $bundle = $this->getMock(BundleInterface::class);
        $bundle->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('/bundle/root'));
        $bundle->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue('Foo\\BarBundle'));

        $expected = ['/bundle/root/Service/SomeService.php', [
            'foo' => 'bar',
            'classname' => 'SomeService',
            'namespace' => 'Foo\\BarBundle\\Service',
        ]];
        $this->assertSame($expected, $this->creator->resolveBundleClass($bundle, $relativeClass, ['foo' => 'bar']));
    }
}
