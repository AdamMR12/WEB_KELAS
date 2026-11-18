<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Profil Kelas</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #1e293b; margin: 24px; }
        .header { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { font-size: 16px; margin: 0 0 4px; text-transform: uppercase; }
        .header p { font-size: 11px; margin: 0; color: #475569; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
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
        <h1>Laporan Profil Kelas</h1>
        <p>Politeknik Negeri Subang &bull; Dicetak pada {{ now()->format('d/m/Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 85px;">ID Kelas</th>
                <th>Nama Kelas</th>
                <th>Program Studi</th>
                <th style="width: 80px;">Angkatan</th>
                <th style="width: 110px;">Jumlah Mahasiswa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarKelas as $idx => $kls)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $kls->ID_Kelas }}</td>
                    <td>{{ $kls->Nama_Kelas }}</td>
                    <td>{{ $kls->Program_Studi }}</td>
                    <td>{{ $kls->Angkatan }}</td>
                    <td>{{ $kls->mahasiswa_count }} Mahasiswa</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada data profil kelas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak melalui Modul Struktur Kelas &amp; Dashboard (Kelompok 4)
    </div>
</body>
</html>
