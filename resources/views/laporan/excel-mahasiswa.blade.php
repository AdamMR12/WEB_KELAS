<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Mahasiswa</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="7" style="font-weight:bold; font-size:14px; text-align:center;">
                    LAPORAN DATA MAHASISWA &amp; PENGURUS KELAS
                </th>
            </tr>
            <tr>
                <th style="background-color:#e2e8f0;">No</th>
                <th style="background-color:#e2e8f0;">NIM</th>
                <th style="background-color:#e2e8f0;">Nama Mahasiswa</th>
                <th style="background-color:#e2e8f0;">Kelas</th>
                <th style="background-color:#e2e8f0;">Program Studi</th>
                <th style="background-color:#e2e8f0;">Jabatan</th>
                <th style="background-color:#e2e8f0;">No. WhatsApp</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataMahasiswa as $idx => $mhs)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td style="mso-number-format:'\@';">{{ $mhs->NIM }}</td>
                    <td>{{ $mhs->Nama_Mahasiswa }}</td>
                    <td>{{ $mhs->kelas->Nama_Kelas ?? $mhs->ID_Kelas }}</td>
                    <td>{{ $mhs->kelas->Program_Studi ?? '-' }}</td>
                    <td>{{ $mhs->pengurus?->jabatan?->Nama_Jabatan ?? 'Anggota' }}</td>
                    <td style="mso-number-format:'\@';">{{ $mhs->pengurus->No_WhatsApp ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
