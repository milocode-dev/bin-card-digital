<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Item Page</title>
</head>
<body>
    <form action="{{ route('item.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="name">Nama Barang</label>
        <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}">

        <label for="code">Kode Barang</label>
        <input type="text" id="code" name="code" value="{{ old('code', $item->code) }}">

        <label for="unit">Satuan</label>
        <input type="text" id="unit" name="unit" value="{{ old('unit', $item->unit) }}">

        <label for="location">Posisi</label>
        <select name="location" id="location">
            @foreach ($location as $loc)
                <option value="{{ $loc }}" @selected(old('location', $item->location) == $loc)>{{ $loc }}</option>
            @endforeach
        </select>

        <label for="min_stock">Stock Minimal</label>
        <input type="text" id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}">

        <label for="stock">Stock</label>
        <input type="text" id="stock" name="stock" value="{{ old('stock', $item->stock) }}">

        <button type="submit">Update Barang</button>
    </form>
</body>
</html>