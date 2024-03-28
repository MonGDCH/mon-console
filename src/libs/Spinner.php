<?php

declare(strict_types=1);

namespace mon\console\libs;

/**
 * Loading加载中
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Spinner
{
    const HIDE_CURSOR_SEQ = "\033[?25l";
    const SHOW_CURSOR_SEQ = "\033[?25h";

    const CHARS = ['⠏', '⠛', '⠹', '⢸', '⣰', '⣤', '⣆', '⡇'];
    //  const CHARS = ["-", "\\", "|", "/"];

    const COLORS = [
        196, 196, 202, 202, 208, 208, 214, 214, 220, 220, 226, 226, 190, 190, 154, 154, 118, 118,
        82, 82, 46, 46, 47, 47, 48, 48, 49, 49, 50, 50, 51, 51, 45, 45, 39, 39, 33, 33, 27, 27, 56,
        56, 57, 57, 93, 93, 129, 129, 165, 165, 201, 201, 200, 200, 199, 199, 198, 198, 197, 197,
    ];

    /** @var integer */
    private $currentCharIdx = 0;
    /** @var integer */
    private $currentColorIdx = 0;
    /** @var integer */
    private $framesCount;
    /** @var integer */
    private $colorCount;
    /** @var false|resource */
    private $stream = STDERR;
    /** @var Spinner */
    private static $instance = null;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->framesCount = count(self::CHARS);
        $this->colorCount = count(self::COLORS);
    }

    /**
     * 单例实现
     *
     * @return Spinner
     */
    public static function instance(): Spinner
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * 渲染loading图标
     *
     * @return void
     */
    public function spin(): void
    {
        $this->write(
            $this->eraseSequence(),
            $this->frameSequence(
                self::COLORS[$this->currentColorIdx],
                self::CHARS[$this->currentCharIdx]
            ),
            $this->moveBackSequence()
        );
        $this->update();
    }

    /**
     * 开始渲染
     *
     * @return void
     */
    public function begin(): void
    {
        $this->hideCursor();
    }

    /**
     * 渲染结束
     *
     * @return void
     */
    public function end(): void
    {
        $this->erase();
        $this->showCursor();
    }

    /**
     * 清除渲染
     *
     * @return void
     */
    public function erase(): void
    {
        $this->write(
            $this->eraseSequence()
        );
    }

    private function moveBackSequence(): string
    {
        return "\033[1D";
    }

    private function eraseSequence(): string
    {
        return "\033[1X";
    }

    private function frameSequence(int $fg, string $char): string
    {
        return "\033[38;5;{$fg}m{$char}\033[0m";
    }

    private function hideCursor(): void
    {
        $this->write(self::HIDE_CURSOR_SEQ);
    }

    private function write(string ...$text): void
    {
        foreach ($text as $s) {
            if (false === $this->stream) {
                echo $s;
            } elseif (false === @fwrite($this->stream, $s)) {
                throw new \RuntimeException('Unable to write stream.');
            }
        }
        if (false !== $this->stream) {
            fflush($this->stream);
        }
    }

    private function showCursor(): void
    {
        $this->write(self::SHOW_CURSOR_SEQ);
    }

    private function update(): void
    {
        if (++$this->currentCharIdx === $this->framesCount) {
            $this->currentCharIdx = 0;
        }
        if (++$this->currentColorIdx === $this->colorCount) {
            $this->currentColorIdx = 0;
        }
    }
}
