<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Mahasiswa</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #1e293b; margin: 24px; }
        .header { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { font-size: 16px; margin: 0 0 4px; text-transform: uppercase; }
        .header p { font-size: 11px; margin: 0; color: #475569; }
        .meta { margin-bottom: 12px; font-size: 11px; color: #334155; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px 9px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        td { font-size: 11px; }
        .footer { margin-top: 24px; text-align: right; font-size: 11px; color: #475569; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body @if(!empty($autoPrint)) onload="window.print()" @endif>
    @if(!empty($autoPrint))
        <div class="no-print" style="margin-bottom: 14px; text-align: right;">
            <button onclick="window.print()" style="padding: 8px 14px; background: #4f46e5; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                Simpan / Cetak PDF
            </button>
        </div>
    @endif

    <div class="header">
        <h1>Laporan Data Mahasiswa &amp; Kepengurusan Kelas</h1>
        <p>Politeknik Negeri Subang &bull; Sistem Informasi Website Kelas</p>
    </div>

    <div class="meta">
        <strong>Kelas:</strong> {{ $kelasDipilih ? $kelasDipilih->Nama_Kelas . ' (' . $kelasDipilih->Program_Studi . ' - ' . $kelasDipilih->Angkatan . ')' : 'Semua Kelas' }}<br>
        <strong>Total Data:</strong> {{ $dataMahasiswa->count() }} Mahasiswa &bull;
        <strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }} WIB
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 32px;">No</th>
                <th style="width: 95px;">NIM</th>
                <th>Nama Mahasiswa</th>
                <th style="width: 90px;">Kelas</th>
                <th style="width: 125px;">Jabatan</th>
                <th style="width: 115px;">No. WhatsApp</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataMahasiswa as $idx => $mhs)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $mhs->NIM }}</td>
                    <td>{{ $mhs->Nama_Mahasiswa }}</td>
                    <td>{{ $mhs->kelas->Nama_Kelas ?? $mhs->ID_Kelas }}</td>
                    <td>{{ $mhs->pengurus?->jabatan?->Nama_Jabatan ?? 'Anggota' }}</td>
                    <td>{{ $mhs->pengurus->No_WhatsApp ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data mahasiswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak melalui Modul Struktur Kelas &amp; Dashboard (Kelompok 4)
    </div>
</body>
</html>
