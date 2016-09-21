<?php

namespace Perform\Base\Tests\Email;

use Perform\Base\Email\Mailer;

/**
 * MailerTest
 **/
class MailerTest extends \PHPUnit_Framework_TestCase
{
    protected $swift;
    protected $twig;
    protected $fromAddress;
    protected $logger;
    protected $mailer;

    public function setUp()
    {
        $this->swift = $this->getMockBuilder('Swift_Mailer')
                     ->disableOriginalConstructor()
                     ->getMock();
        $this->twig = $this->getMock('Twig_Environment');
        $this->fromAddress = 'mailer@admin.dev';
        $this->logger = $this->getMock('Psr\Log\LoggerInterface');
        $this->mailer = new Mailer($this->swift, $this->twig, $this->fromAddress, $this->logger);
    }

    public function messageProvider()
    {
        return [
            ['recipient@example.com', 'Test subject', 'test.txt.twig'],
            [['recipient@example.com'], 'Test subject', 'test.html.twig'],
            [['recipient@example.com', 'recipient2@example.com'], 'Another subject', 'test.txt.twig'],
        ];
    }

    /**
     * @dataProvider messageProvider
     */
    public function testCreateMessage($recipient, $subject, $template)
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with($template)
            ->will($this->returnValue('Rendered template'));

        $message = $this->mailer->createMessage($recipient, $subject, $template);
        $this->assertInstanceOf('\Swift_Message', $message);
        $this->assertSame((array) $this->fromAddress, array_keys($message->getFrom()));
        $this->assertSame((array) $recipient, array_keys($message->getTo()));
        $this->assertSame($subject, $message->getSubject());
        $this->assertSame('Rendered template', $message->getBody());
    }

    /**
     * @dataProvider messageProvider
     */
    public function testCreateMessageWithTemplateContext($recipient, $subject, $template)
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with($template, ['foo' => 'bar'])
            ->will($this->returnValue('Rendered template'));

        $message = $this->mailer->createMessage($recipient, $subject, $template, ['foo' => 'bar']);
        $this->assertInstanceOf('\Swift_Message', $message);
        $this->assertSame((array) $this->fromAddress, array_keys($message->getFrom()));
        $this->assertSame((array) $recipient, array_keys($message->getTo()));
        $this->assertSame($subject, $message->getSubject());
        $this->assertSame('Rendered template', $message->getBody());
    }

    public function testSendMessage()
    {
        $message = new \Swift_Message();
        $message->setTo(['test@example.com', 'test2@example.com'])
            ->setSubject('Test subject');
        $this->swift->expects($this->once())
            ->method('send')
            ->with($message)
            ->will($this->returnValue(1));
        $this->logger->expects($this->once())
            ->method('info')
            ->with('Sent email with subject "Test subject" to test@example.com, test2@example.com');

        $this->assertSame(1, $this->mailer->sendMessage($message));
    }

    /**
     * @dataProvider messageProvider
     */
    public function testSend($recipient, $subject, $template)
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with($template, ['foo' => 'bar'])
            ->will($this->returnValue('Rendered template'));
        $this->swift->expects($this->once())
            ->method('send')
            ->will($this->returnValue(1));

        $this->assertSame(1, $this->mailer->send($recipient, $subject, $template, ['foo' => 'bar']));
    }

    public function testSendMessageWithoutLogging()
    {
        $message = new \Swift_Message();
        $mailer = new Mailer($this->swift, $this->twig, $this->fromAddress);
        $this->swift->expects($this->once())
            ->method('send')
            ->with($message)
            ->will($this->returnValue(1));

        $this->assertSame(1, $this->mailer->sendMessage($message));
    }

    public function testCreateMessageFiltersExampleAddresses()
    {
        $this->mailer->setExcludedDomains(['example.com']);
        $message = $this->mailer->createMessage(['test@example.com', 'test@example.co.uk', 'root@localhost', 'root@example.com'], 'test', 'test.twig');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame(['test@example.co.uk', 'root@localhost'], array_keys($message->getTo()));

        $this->mailer->setExcludedDomains(['example.com', 'example.co.uk']);
        $message = $this->mailer->createMessage(['test@example.com', 'test@example.co.uk', 'root@localhost', 'root@example.com'], 'test', 'test.twig');
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame(['root@localhost'], array_keys($message->getTo()));
    }
}
