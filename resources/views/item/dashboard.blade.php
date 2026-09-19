<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Item Page</title>
</head>
<body>
    <h1>Halaman item</h1>
    <a href="{{ route('item.create') }}">+ Tambah data</a>
    <table border 1>
        <tr>
            <th>Gudang</th>
            <th>Nama Barang</th>
            <th>Kode Barang</th>
            <th>Satuan</th>
            <th>Posisi</th>
            <th>Stock Minimal</th>
            <th>Stock</th>
            <th>Aksi</th>
        </tr>

        @foreach ($items as $item)
            <tr>
                <td>{{ $item->warehouse->name }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->code }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->location }}</td>
                <td>{{ $item->min_stock }}</td>
                <td>{{ $item->stock }}</td>
                <td>
                    <a href="{{ route('item.show', $item->id) }}">Detail</a>
                    <a href="{{ route('item.edit', $item->id) }}">Edit</a>

                    <form action="{{ route('item.destroy', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" onclick="confirm('Apakah ingin menghapus barang ini?')">Hapus Data</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>