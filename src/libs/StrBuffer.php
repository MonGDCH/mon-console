<?php

declare(strict_types=1);

namespace mon\console\libs;

/**
 * 字符串对象buffer
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
final class StrBuffer
{
    /**
     * buffer内容
     *
     * @var string
     */
    private $body;

    /**
     * 构造方法
     *
     * @param string $content
     * @return void
     */
    public function __construct(string $content = '')
    {
        $this->body = $content;
    }

    /**
     * 写入内容
     *
     * @param string $content
     * @return void
     */
    public function write(string $content)
    {
        $this->body .= $content;
    }

    /**
     * 从后面添加内容, 写入内容异名方法
     *
     * @param string $content
     * @return void
     */
    public function append(string $content)
    {
        $this->write($content);
    }

    /**
     * 从前面添加内容
     *
     * @param string $content
     * @return void
     */
    public function prepend(string $content)
    {
        $this->body = $content . $this->body;
    }

    /**
     * 清空内容
     *
     * @return void
     */
    public function clear()
    {
        $this->body = '';
    }

    /**
     * 获取内容
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * 设置内容
     *
     * @param string $body
     * @return void
     */
    public function setBody(string $body)
    {
        $this->body = $body;
    }

    /**
     * 获取内容字符串，获取内容异名方法
     *
     * @return void
     */
    public function toString()
    {
        return $this->body;
    }

    /**
     * 字符串输出魔术方法
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
