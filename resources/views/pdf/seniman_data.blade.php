<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Seniman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            text-align: center;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
        }

        th:nth-child(4), td:nth-child(4) {
            width: 30%;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Data Seniman</h1>
    <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
    <table>
        <thead>
            <tr>
                <th>Nama Seniman</th>
                <th>Tingkatan</th>
                <th>Tanggal Lahir</th>
                <th>Deskripsi</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Lama Pengalaman</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($senimanData as $seniman)
                <tr>
                    <td>{{ $seniman->nama_seniman }}</td>
                    <td>{{ $seniman->tingkatan->nama_tingkatan ?? '-' }}</td>
                    <td>{{ $seniman->tgl_lahir }}</td>
                    <td>{{ $seniman->deskripsi_seniman }}</td>
                    <td>{{ $seniman->alamat_seniman }}</td>
                    <td>{{ $seniman->noTelp_seniman }}</td>
                    <td>{{ $seniman->lama_pengalaman }} tahun</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
