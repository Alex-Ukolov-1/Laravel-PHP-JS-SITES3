<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

class ExportController implements FromCollection, WithStrictNullComparison, WithEvents
{
    private $data;
    private $last_horizontal;
    private $last_vertical;

    public function __construct($data, $style = null) {
        ini_set('max_execution_time', '1200');
        ini_set('memory_limit', '1536M');

        $alphabet = array_merge([''], range('A', 'Z'));

        $this->data = $data;
        $this->last_vertical = count($data);
        $this->last_horizontal = $alphabet[count($data[1])];
        $this->defineStyles();

        $this->setStyle($style);

        Sheet::macro('setOrientation', function (Sheet $sheet, $orientation) {
            $sheet->getDelegate()->getPageSetup()->setOrientation($orientation);
        });

        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });
    }

    public function collection() {
        return new Collection($this->data);
    }

    public function setStyle($name = null) {
        if (empty($name)) $name = 'section';
        $this->style = $this->styles[$name];
    }

    private function defineStyles() {
        $this->styles = [];

        $this->styles['trip_xls'] = [
            'A1' => [
                'alignment' => [
                    'horizontal' => 'centerContinuous',
                    'vertical' => 'center'
                ]
            ],
            'A3:A' . $this->last_vertical => [
                'font' => [
                    'bold' => true
                ]
            ],
            'B2:' . $this->last_horizontal . '2' => [
                'font' => [
                    'bold' => true
                ]
            ],
            'B2:' . $this->last_horizontal . $this->last_vertical => [
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center'
                ]
            ],
            'A' . $this->last_vertical => [
                'alignment' => [
                    'horizontal' => 'right',
                    'vertical' => 'center'
                ]
            ],
            'columnsWidth' => [
                'A' => 10,
                'B' => 16,
                'C' => 12,
                'D' => 26,
                'E' => 24,
                'F' => 24,
                'G' => 26,
                'H' => 30,
                'I' => 16,
                'J' => 16,
                'K' => 40,
                'L' => 22,
                'M' => 22,
                'N' => 20,
            ],
            'A1:' . $this->last_horizontal . '1' => 'merge'
        ];

    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {

                foreach ($this->style as $columns => $style) {

                    if ($style === 'merge') {
                        $event->sheet->getDelegate()->mergeCells($columns);
                        continue;
                    }

                    if ($columns === 'columnsWidth') {
                        foreach ($style as $column => $width) {
                            $event->sheet->getDelegate()->getColumnDimension($column)->setWidth($width);
                        }
                        continue;
                    }

                    if ($columns === 'orientation') {
                        $event->sheet->setOrientation($style);
                        continue;
                    }

                    if ($columns === 'size') {
                        $event->sheet->getPageSetup()->setPaperSize($style);
                        continue;
                    }

                    $event->sheet->styleCells($columns, $style);
                }

                $pageMargins = new \PhpOffice\PhpSpreadsheet\Worksheet\PageMargins();
                $pageMargins->setTop(0.5);
                $pageMargins->setRight(0.5);
                $pageMargins->setBottom(0.5);
                $pageMargins->setLeft(0.5);

                $event->sheet->setPageMargins($pageMargins);
            },
        ];
    }

}
