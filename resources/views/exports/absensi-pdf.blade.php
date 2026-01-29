<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Absensi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4F46E5;
        }
        
        .header h1 {
            font-size: 18pt;
            color: #1E40AF;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .header .subtitle {
            font-size: 11pt;
            color: #555;
            margin-bottom: 5px;
        }
        
        .info-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #F3F4F6;
            border-radius: 5px;
        }
        
        .info-section p {
            margin: 3px 0;
            font-size: 9pt;
        }
        
        .info-section strong {
            color: #1E40AF;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table thead {
            background-color: #4F46E5;
            color: white;
        }
        
        table thead th {
            padding: 8px 5px;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            border: 1px solid #3730A3;
        }
        
        table tbody td {
            padding: 6px 5px;
            border: 1px solid #D1D5DB;
            font-size: 8pt;
            vertical-align: middle;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        table tbody tr:hover {
            background-color: #E5E7EB;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-left {
            text-align: left;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
        }
        
        .badge-success {
            background-color: #10B981;
            color: white;
        }
        
        .badge-warning {
            background-color: #F59E0B;
            color: white;
        }
        
        .badge-error {
            background-color: #EF4444;
            color: white;
        }
        
        .badge-info {
            background-color: #3B82F6;
            color: white;
        }
        
        .badge-secondary {
            background-color: #6B7280;
            color: white;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #E5E7EB;
            text-align: center;
            font-size: 8pt;
            color: #6B7280;
        }
        
        .summary {
            margin-top: 10px;
            padding: 10px;
            background-color: #EFF6FF;
            border-left: 4px solid #3B82F6;
        }
        
        .summary p {
            margin: 3px 0;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Absensi Karyawan</h1>
        <p class="subtitle">Periode: {{ $periodeText }}</p>
        @if($karyawanInfo)
            <p class="subtitle">Karyawan: {{ $karyawanInfo->nama_lengkap }} ({{ $karyawanInfo->nip }})</p>
        @endif
        <p style="font-size: 8pt; color: #888; margin-top: 5px;">Tanggal Export: {{ $tanggal_export }}</p>
    </div>

    <div class="info-section">
        <p><strong>Total Data:</strong> {{ $absensis->count() }} absensi</p>
        @if($karyawanInfo)
            <p><strong>Departemen:</strong> {{ $karyawanInfo->departemen->nama_departemen ?? '-' }}</p>
            <p><strong>Jabatan:</strong> {{ $karyawanInfo->jabatan->nama_jabatan ?? '-' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 8%;">NIP</th>
                <th style="width: 14%;">Nama Karyawan</th>
                <th style="width: 11%;">Departemen</th>
                <th style="width: 11%;">Jabatan</th>
                <th style="width: 7%;">Jam Masuk</th>
                <th style="width: 7%;">Jam Pulang</th>
                <th style="width: 8%;">Durasi</th>
                <th style="width: 12%;">Lokasi</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis as $index => $absensi)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                    <td class="text-center">{{ $absensi->karyawan->nip ?? '-' }}</td>
                    <td>{{ $absensi->karyawan->nama_lengkap ?? '-' }}</td>
                    <td>{{ $absensi->karyawan->departemen->nama_departemen ?? '-' }}</td>
                    <td>{{ $absensi->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                    <td class="text-center">
                        @if($absensi->jam_masuk)
                            <span class="badge badge-success">{{ \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($absensi->jam_pulang)
                            <span class="badge badge-error">{{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($absensi->jam_masuk && $absensi->jam_pulang)
                            @php
                                $masuk = \Carbon\Carbon::parse($absensi->jam_masuk);
                                $pulang = \Carbon\Carbon::parse($absensi->jam_pulang);
                                $diff = $masuk->diff($pulang);
                                $durasi = $diff->format('%H:%I');
                            @endphp
                            {{ $durasi }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $absensi->lokasi->nama_lokasi ?? '-' }}</td>
                    <td class="text-center">
                        @php
                            $statusClass = match($absensi->status) {
                                'hadir', 'tepat_waktu' => 'badge-success',
                                'terlambat' => 'badge-warning',
                                'izin', 'cuti' => 'badge-info',
                                'alpha' => 'badge-error',
                                default => 'badge-secondary'
                            };
                            $statusText = match($absensi->status) {
                                'hadir' => 'Hadir',
                                'tepat_waktu' => 'Tepat Waktu',
                                'terlambat' => 'Terlambat',
                                'izin' => 'Izin',
                                'cuti' => 'Cuti',
                                'alpha' => 'Alpha',
                                default => '-'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px;">
                        Tidak ada data absensi
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Ringkasan:</strong></p>
        <p>Total Absensi: <strong>{{ $absensis->count() }}</strong></p>
        <p>Hadir: <strong>{{ $absensis->whereIn('status', ['hadir', 'tepat_waktu'])->count() }}</strong></p>
        <p>Terlambat: <strong>{{ $absensis->where('status', 'terlambat')->count() }}</strong></p>
        <p>Izin: <strong>{{ $absensis->where('status', 'izin')->count() }}</strong></p>
        <p>Cuti: <strong>{{ $absensis->where('status', 'cuti')->count() }}</strong></p>
        <p>Alpha: <strong>{{ $absensis->where('status', 'alpha')->count() }}</strong></p>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Absensi</p>
        <p>© {{ date('Y') }} - Sistem Absensi Karyawan</p>
    </div>
</body>
</html>
