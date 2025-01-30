<?php

namespace Tests\Core;

use App\Core\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{

    /**
     * @return void
     */
    #[\Override]
    protected function setUp(): void
    {
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
    }

    /**
     * @return void
     */
    #[\Override]
    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    /**
     * @return void
     */
    public function testSetMessage(): void
    {
        $message = new Message();
        $message->setMessage('Test message', 'success');
        $this->assertArrayHasKey('message', $_SESSION);
        $this->assertSame('Test message', $_SESSION['message']['messageText']);
        $this->assertSame('success', $_SESSION['message']['messageType']);
    }

    /**
     * @return void
     */
    public function testGetMessageWhenMessageExists(): void
    {
        $message = new Message();
        $_SESSION['message'] = [
            'messageText' => 'Test message',
            'messageType' => 'info',
        ];
        $result = $message->getMessage();
        $this->assertSame('Test message', $result['messageText']);
        $this->assertSame('info', $result['messageType']);
        $this->assertArrayNotHasKey('message', $_SESSION);
    }

    /**
     * @return void
     */
    public function testGetMessageWhenNoMessageExists(): void
    {
        $message = new Message();
        $result = $message->getMessage();
        $this->assertSame('', $result['messageText']);
        $this->assertSame('', $result['messageType']);
    }

}
