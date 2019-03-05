<?php
namespace Mon\console\libs;

/**
 * 字符串对象话
 *
 * @package Inhere\Console\Components
 */
final class StrBuffer
{
    /**
     * @var string
     */
    private $body;

    public function __construct($content = '')
    {
        $this->body = $content;
    }

    /**
     * @param string $content
     */
    public function write(string $content)
    {
        $this->body .= $content;
    }

    /**
     * @param string $content
     */
    public function append(string $content)
    {
        $this->write($content);
    }

    /**
     * @param string $content
     */
    public function prepend(string $content)
    {
        $this->body = $content . $this->body;
    }

    /**
     * clear
     */
    public function clear()
    {
        $this->body = '';
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
