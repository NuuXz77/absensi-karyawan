<?php

namespace App\Exports\Admin\Absensi;

use App\Models\Absensi;
use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class ExportExcel implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents, WithCustomStartCell
{
    protected $karyawan_id;
    protected $tanggal_awal;
    protected $tanggal_akhir;
    protected $periode_text;
    protected $rowCount = 0;

    public function __construct($karyawan_id = null, $tanggal_awal = null, $tanggal_akhir = null, $periode_text = '')
    {
        $this->karyawan_id = $karyawan_id;
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->periode_text = $periode_text;
    }
    
    public function startCell(): string
    {
        return 'A6'; // Header dimulai dari A6, data dari A7
    }

    public function collection()
    {
        $query = Absensi::with(['karyawan.departemen', 'karyawan.jabatan', 'lokasi']);

        if ($this->karyawan_id) {
            $query->where('karyawan_id', $this->karyawan_id);
        }

        // Filter tanggal dengan whereBetween karena sudah di-handle di component
        if ($this->tanggal_awal && $this->tanggal_akhir) {
            $query->whereBetween('tanggal', [$this->tanggal_awal, $this->tanggal_akhir]);
        } elseif ($this->tanggal_awal) {
            $query->whereDate('tanggal', $this->tanggal_awal);
        }
        
        // Order by tanggal dan jam masuk
        $query->orderBy('tanggal', 'asc')
              ->orderBy('jam_masuk', 'asc');

        $collection = $query->get();
        $this->rowCount = $collection->count();
        return $collection;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'NIP',
            'Nama Karyawan',
            'Departemen',
            'Jabatan',
            'Jam Masuk',
            'Jam Pulang',
            'Durasi Kerja',
            'Lokasi',
            'Status',
            'Latitude Masuk',
            'Longitude Masuk',
            'Latitude Keluar',
            'Longitude Keluar',
        ];
    }

    public function map($absensi): array
    {
        static $no = 0;
        $no++;

        $status = match($absensi->status) {
            'hadir' => 'Hadir',
            'tepat_waktu' => 'Tepat Waktu',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'cuti' => 'Cuti',
            'alpha' => 'Alpha',
            default => '-'
        };
        
        // Hitung durasi kerja
        $durasi = '-';
        if ($absensi->jam_masuk && $absensi->jam_pulang) {
            $masuk = Carbon::parse($absensi->jam_masuk);
            $pulang = Carbon::parse($absensi->jam_pulang);
            $diff = $masuk->diff($pulang);
            $durasi = $diff->format('%H jam %I menit');
        }

        return [
            $no,
            Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('DD MMMM YYYY'),
            $absensi->karyawan->nip ?? '-',
            $absensi->karyawan->nama_lengkap ?? '-',
            $absensi->karyawan->departemen->nama_departemen ?? '-',
            $absensi->karyawan->jabatan->nama_jabatan ?? '-',
            $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk)->format('H:i:s') : '-',
            $absensi->jam_pulang ? Carbon::parse($absensi->jam_pulang)->format('H:i:s') : '-',
            $durasi,
            $absensi->lokasi->nama_lokasi ?? '-',
            $status,
            $absensi->lat_masuk ?? '-',
            $absensi->long_masuk ?? '-',
            $absensi->lat_keluar ?? '-',
            $absensi->long_keluar ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $headerRow = 6; // Row header dimulai dari row 6
        $lastRow = $headerRow + $this->rowCount;
        
        return [
            // Header table
            $headerRow => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 18,
            'C' => 15,
            'D' => 25,
            'E' => 20,
            'F' => 20,
            'G' => 12,
            'H' => 12,
            'I' => 15,
            'J' => 25,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 15,
            'O' => 15,
        ];
    }
    
    public function title(): string
    {
        return 'Data Absensi';
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Merge dan style untuk judul
                $sheet->mergeCells('A1:O1');
                $sheet->setCellValue('A1', 'LAPORAN DATA ABSENSI KARYAWAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension('1')->setRowHeight(30);
                
                // Info periode
                $sheet->mergeCells('A2:O2');
                $sheet->setCellValue('A2', 'Periode: ' . $this->periode_text);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Info karyawan (jika ada, row 3 akan digunakan, jika tidak skip)
                $infoRow = 3;
                if ($this->karyawan_id) {
                    $karyawan = Karyawan::find($this->karyawan_id);
                    if ($karyawan) {
                        $sheet->mergeCells('A3:O3');
                        $sheet->setCellValue('A3', 'Karyawan: ' . $karyawan->nama_lengkap . ' (' . $karyawan->nip . ')');
                        $sheet->getStyle('A3')->applyFromArray([
                            'font' => ['size' => 11],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        $infoRow = 4;
                    }
                }
                
                // Tanggal export
                $sheet->mergeCells('A' . $infoRow . ':O' . $infoRow);
                $sheet->setCellValue('A' . $infoRow, 'Tanggal Export: ' . Carbon::now()->locale('id')->isoFormat('DD MMMM YYYY, HH:mm'));
                $sheet->getStyle('A' . $infoRow)->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Empty row sebelum header (row 5)
                
                // Border untuk semua data termasuk header
                $lastRow = 6 + $this->rowCount;
                $sheet->getStyle('A6:O' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
                
                // Zebra striping untuk data rows
                for ($i = 7; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A' . $i . ':O' . $i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                        ]);
                    }
                }
                
                // Auto-height untuk semua rows
                foreach ($sheet->getRowIterator() as $row) {
                    $sheet->getRowDimension($row->getRowIndex())->setRowHeight(-1);
                }
                
                // Freeze pane di header
                $sheet->freezePane('A7');
            },
        ];
    }
}
