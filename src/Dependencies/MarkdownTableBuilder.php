<?php

namespace AppManager\Dependencies;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class MarkdownTableBuilder implements Renderable
{

    protected $headers = [];

    protected $alignments = [];

    protected $headerSpan = [];

    protected $rows = [];


    /**
     * Set column headers
     *
     * @param array $headers
     * @return $this
     */
    public function headers($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set column alignment
     *
     * @param $alignments
     * @return $this
     */
    public function align($alignments = null)
    {
        $this->alignments = $alignments;

        return $this;
    }

    /**
     * Set column alignment
     *
     * @param $headerSpan
     * @return $this
     */
    public function headSpan($headerSpan = null)
    {
        $this->headerSpan = $headerSpan;

        return $this;
    }

    /**
     * Add one row to the table
     *
     * @param $row
     * @return $this
     */
    public function row($row)
    {
        $this->rows[] = $row;

        return $this;
    }

    /**
     * Add multiple rows
     *
     * @param $rows
     * @return $this
     */
    public function rows($rows)
    {
        if (is_array($rows))
            $rows = array_values($rows);
        else if ($rows instanceof Collection)
            $rows = $rows->values()->toArray();
        else
            $rows = [];

        $this->rows = array_values(array_merge($this->rows, $rows));

        return $this;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        $widths = $this->calculateWidths();

        $table = $this->renderHeaders($widths);
        $table .= $this->renderRows($widths);

        return $table;
    }

    protected function calculateWidths()
    {
        $widths = [];

        foreach (array_merge([$this->headers], $this->rows) as $row) {
            for ($i = 0; $i < count($row); $i++) {
                $iWidth = mb_strlen((string)$row[$i]);
                if ((!array_key_exists($i, $widths)) || $iWidth > $widths[$i]) {
                    $widths[$i] = $iWidth;
                }
            }
        }

        // all columns must be at least 3 wide for the markdown to work
        return array_map(function ($width) {
            return max($width, 3);
        }, $widths);
    }

    protected function renderHeaders($widths)
    {
        $result = '| ';
        for ($i = 0; $i < count($this->headers); $i++) {
            if (is_array($this->headerSpan) && count($this->headerSpan) > $i) {
                $span = $this->headerSpan[$i];

                $offset = 0;
                if ($i > 0)
                    for ($x = 0; $x < $i; $x++)
                        $offset += $this->headerSpan[$x];

                $width = array_sum(array_slice($widths, $offset, $span)) + ($span > 1 ? (($span - 1) * 3) : 0);
            } else
                $width = $widths[$i];

            $result .= $this->renderCell($this->headers[$i], $this->columnAlign($i), $width) . ' | ';
        }

        return rtrim($result, ' ') . PHP_EOL;
    }

    protected function renderCell($contents, $alignment, $width)
    {
        switch ($alignment) {
            case 'L':
                $type = STR_PAD_RIGHT;
                break;
            case 'C':
                $type = STR_PAD_BOTH;
                break;
            case 'R':
                $type = STR_PAD_LEFT;
                break;
        }

        return str_pad($contents, $width, ' ', $type);
    }

    protected function columnAlign($columnNumber)
    {
        $valid = ['L', 'C', 'R'];

        if (array_key_exists($columnNumber, $this->alignments) && in_array($this->alignments[$columnNumber], $valid)) {
            return $this->alignments[$columnNumber];
        }

        return 'L';
    }

    protected function renderRows($widths)
    {
        $result = '';
        foreach ($this->rows as $row) {
            $result .= '| ';
            for ($i = 0; $i < count($row); $i++) {
                $result .= $this->renderCell($row[$i], $this->columnAlign($i), $widths[$i]) . ' | ';
            }
            $result = rtrim($result, ' ') . PHP_EOL;
        }

        return $result;
    }

    protected function renderAlignments($widths)
    {
        $row = '|';
        for ($i = 0; $i < count($widths); $i++) {
            $cell = str_repeat('-', $widths[$i] + 2);
            $align = $this->columnAlign($i);

            if ($align == 'C') {
                $cell = ':' . substr($cell, 2) . ':';
            }

            if ($align == 'R') {
                $cell = substr($cell, 1) . ':';
            }

            $row .= $cell . '|';
        }

        return $row;
    }


}
