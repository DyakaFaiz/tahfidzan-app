<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Facades\Excel;
class BlangkoExports implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        // Data yang ingin dimulai pada baris ke-4, setelah header
        $data = collect([
            [
                '1',    // 1. NO
                'John Doe', // 2. NAMA
                '12',   // 3. ZIADAH
                '42',   // 4. JML(POJOK) - AWAL
                '12',   // 5. DERESAN A - AWAL
                '42',   // 6. JML(POJOK) - AKHIR
                '4',    // 7. DERESAN B - AWAL
                '64',   // 8. JML(POJOK) - AKHIR
                '5',    // 9. TOTAL DERESAN
                '7',    // 10. LEVEL DERESAN
                '35',   // 11. BIN NADHOR
                '46',   // 12. KEHADIRAN
                '56',   // 13. (Tambahan 1)
                '5',    // 14. (Tambahan 2)
                '2',    // 15. (Tambahan 3)
                '4',    // 16. (Tambahan 4)
                '3',    // 17. (Tambahan 5)
                '123',  // 18. (Tambahan 6)
                '123',  // 19. (Tambahan 7)
                '2',    // 20. (Tambahan 8)
                '3',    // 21. (Tambahan 9)
                '1',    // 22. (Tambahan 10)
                '0',    // 23. (Tambahan 11)
                '3',    // 24. (Tambahan 12)
                '2'     // 25. (Tambahan 13)
            ]
        ]);

        // Menambahkan 3 baris kosong di atas data yang ada
        $emptyRows = collect([[], [], []]);

        return $emptyRows->merge($data)->values(); // Gabungkan dan rapikan indeks
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge Header Cells (Row 1)
                // $sheet->mergeCells('C1:F1');
                // $sheet->mergeCells('H1:K1');
                // $sheet->mergeCells('M1:P1');
                // $sheet->mergeCells('T1:U1');
                // $sheet->mergeCells('V1:Y1');

                // $sheet->mergeCells('A1:A3');
                // $sheet->mergeCells('B1:B3');
                // $sheet->mergeCells('G1:G3');
                // $sheet->mergeCells('L1:L3');
                // $sheet->mergeCells('Q1:Q3');
                // $sheet->mergeCells('R1:R3');
                // $sheet->mergeCells('S1:S3');

                // $sheet->mergeCells('T1:T2');
                // $sheet->mergeCells('U1:U2');

                // $sheet->mergeCells('V1:V2');
                // $sheet->mergeCells('W1:W2');
                // $sheet->mergeCells('X1:X2');
                // $sheet->mergeCells('Y1:Y2');
                
                // $sheet->setCellValue('C1', 'ZIADAH');
                // $sheet->setCellValue('L1', 'JML(POJOK)');
                
                // $sheet->setCellValue('Q1', 'JML(POJOK)');
                // $sheet->setCellValue('R1', 'TOTAL DERESAN(POJOK)');
                // $sheet->setCellValue('S1', 'LEVEL DERESAN');
                // // Set label untuk merged cells
                // $sheet->setCellValue('G1', 'JML(POJOK)');
                // $sheet->setCellValue('H1', 'DERESAN A');
                // $sheet->setCellValue('M1', 'DERESAN B');
                // $sheet->setCellValue('T1', 'BIN NADHOR');
                // $sheet->setCellValue('V1', 'KEHADIRAN');
                
                // $sheet->mergeCells('C2:D2');
                // $sheet->setCellValue('C2', 'AWAL');

                // $sheet->mergeCells('E2:F2');
                // $sheet->setCellValue('E2', 'AKHIR');

                // $sheet->mergeCells('H2:I2');
                // $sheet->setCellValue('H2', 'AWAL');

                // $sheet->mergeCells('J2:K2');
                // $sheet->setCellValue('J2', 'AKHIR');

                // $sheet->mergeCells('M2:N2');
                // $sheet->setCellValue('M2', 'AWAL');

                // $sheet->mergeCells('O2:P2');
                // $sheet->setCellValue('O2', 'AKHIR');


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
