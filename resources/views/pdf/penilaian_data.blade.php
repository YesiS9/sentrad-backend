<!DOCTYPE html>
<html>
<head>
    <title>Penilaian Karya</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .page-break {
            page-break-after: always;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @foreach($penilaianData as $data)
        <h1>Data Penilaian Karya</h1>
        <table>
            <tr>
                <th>Nama Penilai</th>
                <td>{{ $data['nama_penilai'] }}</td>
            </tr>
            <tr>
                <th>Nama / Nama Kelompok</th>
                <td>{{ $data['nama'] }}</td>
            </tr>
            <tr>
                <th>Nama Tingkatan</th>
                <td>{{ $data['tingkatan'] }}</td>
            </tr>
            <tr>
                <th>Total Nilai</th>
                <td>{{ $data['total_nilai'] }}</td>
            </tr>
            <tr>
                <th>Komentar</th>
                <td>{{ $data['komentar'] }}</td>
            </tr>
            <tr>
                <th>Tanggal Penilaian</th>
                <td>{{ $data['created_at'] }}</td>
            </tr>
        </table>

        <h3 class="section-title">Detail Penilaian</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama Rubrik</th>
                    <th>Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['rubrik_penilaian'] as $rubrik)
                    <tr>
                        <td>{{ $rubrik['nama_rubrik'] }}</td>
                        <td>{{ $rubrik['skor'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>
    @endforeach
</body>
</html>
