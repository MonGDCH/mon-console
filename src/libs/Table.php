<?php

namespace Mon\console\libs;

use Mon\console\libs\Util;
use Mon\console\libs\StrBuffer;
use Mon\console\libs\Show;

/**
 * 控制台表格库
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Table
{
    /**
     * 表格数据信息展示
     *
     * @param  array $data
     * @param  string $title
     * @param  array $opts
     * @example
     * ```
     * $data = [
     *  [ col1 => value1, col2 => value2, col3 => value3, ... ], // first row
     *  [ col1 => value4, col2 => value5, col3 => value6, ... ], // second row
     *  ... ...
     * ];
     * Table::create($data, 'a table');
     *
     * // use custom head
     * $data = [
     *  [ value1, value2, value3, ... ], // first row
     *  [ value4, value5, value6, ... ], // second row
     *  ... ...
     * ];
     * $opts = [
     *   'showBorder' => true,
     *   'columns' => [col1, col2, col3, ...]
     * ];
     * Table::create($data, 'a table', $opts);
     * ```
     * @return int
     */
    public static function create(array $data, $title = 'Data Table', array $opts = [])
    {
        if (!$data) {
            return -404;
        }

        $buf = new StrBuffer();
        $opts = array_merge([
            'showBorder' => true,
            'leftIndent' => '  ',
            'titlePos' => 'l',
            'titleStyle' => 'bold',
            'headStyle' => 'comment',
            'headBorderChar' => '=',
            'bodyStyle' => '',
            'rowBorderChar' => '-',
            'colBorderChar' => '|',
            'columns' => [],
        ], $opts);

        $hasHead = false;
        $rowIndex = 0;
        $head = [];
        $tableHead = $opts['columns'];
        $leftIndent = $opts['leftIndent'];
        $showBorder = $opts['showBorder'];
        $rowBorderChar = $opts['rowBorderChar'];
        $colBorderChar = $opts['colBorderChar'];

        $info = [
            'rowCount' => count($data),
            'columnCount' => 0,     // 表中有多少列
            'columnMaxWidth' => [], // 表列最大数
            'tableWidth' => 0,      // 表格宽度
        ];

        // 解析表格数据
        foreach ($data as $row) {
            if ($rowIndex === 0) {
                $head = $tableHead ?: array_keys($row);
                $info['columnCount'] = count($row);
                foreach ($head as $index => $name) {
                    if (is_string($name)) {
                        $hasHead = true;
                    }

                    $info['columnMaxWidth'][$index] = mb_strlen($name, 'UTF-8');
                }
            }

            $colIndex = 0;

            foreach ((array) $row as $value) {
                if (isset($info['columnMaxWidth'][$colIndex])) {
                    $colWidth = mb_strlen($value, 'UTF-8');

                    if ($colWidth > $info['columnMaxWidth'][$colIndex]) {
                        $info['columnMaxWidth'][$colIndex] = $colWidth;
                    }
                } else {
                    $info['columnMaxWidth'][$colIndex] = mb_strlen($value, 'UTF-8');
                }

                $colIndex++;
            }

            $rowIndex++;
        }

        $tableWidth = $info['tableWidth'] = array_sum($info['columnMaxWidth']);
        $columnCount = $info['columnCount'];

        if ($title) {
            $tStyle = $opts['titleStyle'] ?: 'bold';
            $title = ucwords(trim($title));
            $titleLength = mb_strlen($title, 'UTF-8');
            $indentSpace = str_pad(' ', ceil($tableWidth / 2) - ceil($titleLength / 2) + ($columnCount * 2), ' ');
            $buf->write("  {$indentSpace}<$tStyle>{$title}</$tStyle>\n");
        }

        $border = $leftIndent . str_pad($rowBorderChar, $tableWidth + ($columnCount * 3) + 2, $rowBorderChar);

        if ($showBorder) {
            $buf->write($border . "\n");
        } else {
            $colBorderChar = '';
        }

        if ($hasHead) {
            $headStr = "{$leftIndent}{$colBorderChar} ";

            foreach ($head as $index => $name) {
                $colMaxWidth = $info['columnMaxWidth'][$index];
                $name = str_pad($name, $colMaxWidth, ' ');
                $name = Util::wrapTag($name, $opts['headStyle']);
                $headStr .= " {$name} {$colBorderChar}";
            }

            $buf->write($headStr . "\n");

            if ($headBorderChar = $opts['headBorderChar']) {
                $headBorder = $leftIndent . str_pad($headBorderChar, $tableWidth + ($columnCount * 3) + 2, $headBorderChar);
                $buf->write($headBorder . "\n");
            }
        }

        $rowIndex = 0;

        foreach ($data as $row) {
            $colIndex = 0;
            $rowStr = "  $colBorderChar ";

            foreach ((array) $row as $value) {
                $colMaxWidth = $info['columnMaxWidth'][$colIndex];
                $value = str_pad($value, $colMaxWidth, ' ');
                $value = Util::wrapTag($value, $opts['bodyStyle']);
                $rowStr .= " {$value} {$colBorderChar}";
                $colIndex++;
            }

            $buf->write($rowStr . "\n");
            $rowIndex++;
        }

        if ($showBorder) {
            $buf->write($border . "\n");
        }

        Show::write($buf);
        return 0;
    }
}
