<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Registrasi</title>
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
            word-wrap: break-word;
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
    <div>
        <h1>Data Registrasi Individu</h1>
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Tgl Lahir</th>
                    <th>Tgl Mulai</th>
                    <th>Alamat</th>
                    <th>No. Telp</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Tgl Registrasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($individuData as $individu)
                    <tr>
                        <td>{{ $individu->nama }}</td>
                        <td>{{ $individu->kategoriSeni->nama_kategori ?? '-' }}</td>
                        <td>{{ $individu->tgl_lahir }}</td>
                        <td>{{ $individu->tgl_mulai }}</td>
                        <td>{{ $individu->alamat }}</td>
                        <td>{{ $individu->noTelp }}</td>
                        <td>{{ $individu->email }}</td>
                        <td>{{ $individu->status_individu }}</td>
                        <td>{{ $individu->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pisah Halaman -->
    <div style="page-break-after: always;"></div>

    <!-- Halaman Kedua: Registrasi Kelompok -->
    <div>
        <h1>Data Registrasi Kelompok</h1>
        <table>
            <thead>
                <tr>
                    <th>Nama Kelompok</th>
                    <th>Kategori</th>
                    <th>Tgl Terbentuk</th>
                    <th>Alamat Kelompok</th>
                    <th>No. Telp Kelompok</th>
                    <th>Email Kelompok</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Anggota</th>
                    <th>Status</th>
                    <th>Tgl Registrasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kelompokData as $kelompok)
                    <tr>
                        <td>{{ $kelompok->nama_kelompok }}</td>
                        <td>{{ $kelompok->kategoriSeni->nama_kategori ?? '-' }}</td>
                        <td>{{ $kelompok->tgl_terbentuk }}</td>
                        <td>{{ $kelompok->alamat_kelompok }}</td>
                        <td>{{ $kelompok->noTelp_kelompok }}</td>
                        <td>{{ $kelompok->email_kelompok }}</td>
                        <td>{{ $kelompok->deskripsi_kelompok }}</td>
                        <td>{{ $kelompok->jumlah_anggota }}</td>
                        <td>{{ $kelompok->status_kelompok }}</td>
                        <td>{{ $kelompok->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
