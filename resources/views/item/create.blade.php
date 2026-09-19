<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ route('item.store') }}" method="POST">
        @csrf
        <label for="name">Nama Barang</label>
        <input type="text" id="name" name="name">

        <label for="code">Kode Barang</label>
        <input type="text" id="code" name="code">

        <label for="unit">Satuan</label>
        <input type="text" id="unit" name="unit">

        <label for="location">Posisi</label>
        <select name="location" id="location">
            @foreach ($location as $loc)
                <option value="{{ $loc }}">{{ $loc }}</option>
            @endforeach
        </select>

        <label for="min_stock">Stock Minimal</label>
        <input type="text" id="min_stock" name="min_stock">

        <label for="stock">Stock</label>
        <input type="text" id="stock" name="stock">

        <button type="submit">Tambah Barang</button>
    </form>
</body>
</html>