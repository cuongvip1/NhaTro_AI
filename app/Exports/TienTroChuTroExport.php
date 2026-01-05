<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TienTroChuTroExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected Collection $rows;
    protected float $grandTotal;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows->values();
        $this->grandTotal = $this->rows->sum(fn ($row) => (float) ($row['tong_tien'] ?? 0));
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Tên phòng', 'Người ở', 'Tiền trọ', 'Tiền dịch vụ', 'Tổng tiền'];
    }

    public function map($row): array
    {
        return [
            $row['ten_phong'] ?? '',
            $row['nguoi_o'] ?? '',
            (float) ($row['tien_tro'] ?? 0),
            (float) ($row['tien_dich_vu'] ?? 0),
            (float) ($row['tong_tien'] ?? 0),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $dataRowCount = $this->rows->count();
                $summaryRow = $dataRowCount + 2; // 1 heading row + data + summary

                $sheet->setCellValue("A{$summaryRow}", 'Tổng tiền tất cả phòng');
                $sheet->mergeCells("A{$summaryRow}:D{$summaryRow}");
                $sheet->setCellValue("E{$summaryRow}", $this->grandTotal);

                $sheet->getStyle("A{$summaryRow}:E{$summaryRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'EEF2FF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);

                $sheet->getStyle("A1:E1")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}
