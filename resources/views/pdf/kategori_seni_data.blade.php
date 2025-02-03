<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori Seni</title>
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
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Data Kategori Seni</h1>
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategoriSeni as $item)
                <tr>
                    <td>{{ $item->nama_kategori }}</td>
                    <td>{{ $item->deskripsi_kategori }}</td>
                    <td>{{ $item->created_at ? $item->created_at->format('d-m-Y') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
