<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penilai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Penilai</h1>
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Penilai</th>
                <th>Nama Kategori</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Bidang Ahli</th>
                <th>Lembaga</th>
                <th>Kuota</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penilaiData as $penilai)
                <tr>
                    <td>{{ $penilai['nama_penilai'] }}</td>
                    <td>{{ $penilai['nama_kategori'] ?? '-' }}</td>
                    <td>{{ $penilai['tgl_lahir'] }}</td>
                    <td>{{ $penilai['alamat_penilai'] }}</td>
                    <td>{{ $penilai['noTelp_penilai'] }}</td>
                    <td>{{ $penilai['bidang_ahli'] }}</td>
                    <td>{{ $penilai['lembaga'] }}</td>
                    <td>{{ $penilai['kuota'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
