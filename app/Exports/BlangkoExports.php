<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BlangkoExports implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        // Data yang ingin dimulai pada baris ke-4, setelah header
        $data = collect([
            ['1', 'John Doe', '12', '42', '12', '42', '4', '64', '5', '7', '35', '46', '56', '5', '2', '4', '3', '123', '123', '2', '3', '1', '0', '3', '2']
        ]);

        return collect([[], []])->merge($data);
    }

    public function headings(): array
    {
        return ['NO', 'NAMA', 'ZIADAH','JML(POJOK)', 'DERESAN A','JML(POJOK)', 'DERESAN B','JML(POJOK)', 'TOTAL DERESAN', 'LEVEL DERESAN', 'BIN NADHOR', 'KEHADIRAN'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge Header Cells (Row 1)
                $sheet->mergeCells('C1:F1');
                $sheet->mergeCells('H1:K1');
                $sheet->mergeCells('M1:P1');
                $sheet->mergeCells('T1:U1');
                $sheet->mergeCells('V1:Y1');

                $sheet->mergeCells('A1:A3');
                $sheet->mergeCells('B1:B3');
                $sheet->mergeCells('G1:G3');
                $sheet->mergeCells('L1:L3');
                $sheet->mergeCells('Q1:Q3');
                $sheet->mergeCells('R1:R3');
                $sheet->mergeCells('S1:S3');

                $sheet->mergeCells('T1:T2');
                $sheet->mergeCells('U1:U2');

                $sheet->mergeCells('V1:V2');
                
                $sheet->setCellValue('C1', 'ZIADAH');
                $sheet->setCellValue('L1', 'JML(POJOK)');
                
                $sheet->setCellValue('Q1', 'JML(POJOK)');
                $sheet->setCellValue('R1', 'TOTAL DERESAN(POJOK)');
                $sheet->setCellValue('S1', 'LEVEL DERESAN');
                // Set label untuk merged cells
                $sheet->setCellValue('G1', 'JML(POJOK)');
                $sheet->setCellValue('H1', 'DERESAN A');
                $sheet->setCellValue('M1', 'DERESAN B');
                $sheet->setCellValue('T1', 'BIN NADHOR');
                $sheet->setCellValue('V1', 'KEHADIRAN');
                
                $sheet->mergeCells('C2:D2');
                $sheet->setCellValue('C2', 'AWAL');

                $sheet->mergeCells('E2:F2');
                $sheet->setCellValue('E2', 'AKHIR');

                $sheet->mergeCells('H2:I2');
                $sheet->setCellValue('H2', 'AWAL');

                $sheet->mergeCells('J2:K2');
                $sheet->setCellValue('J2', 'AKHIR');

                $sheet->mergeCells('M2:N2');
                $sheet->setCellValue('M2', 'AWAL');

                $sheet->mergeCells('O2:P2');
                $sheet->setCellValue('O2', 'AKHIR');


                // Set Column Names and Make Them Bold
                $sheet->getStyle('A1:X1')->getFont()->setBold(true);
                $sheet->getStyle('A2:X2')->getFont()->setBold(true);
                $sheet->getStyle('A3:X3')->getFont()->setBold(true);

                // Center align headers and sub-headers
                $sheet->getStyle('A1:X3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:X3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
