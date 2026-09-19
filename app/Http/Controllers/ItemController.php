<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::all();

        return view('item.dashboard', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $location = ['Sisi A', 'Sisi B', 'Sisi C'];

        return view('item.create', compact('location'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'string|required',
            'code' => 'string|nullable',
            'unit' => 'string|nullable',
            'location' => 'string|nullable',
            'min_stock' => 'integer|nullable',
            'stock' => 'integer|required',
        ]);

        $validatedData['warehouse_id'] = Auth::user()->warehouse_id;

        Item::create($validatedData);

        return redirect()->route('item.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);

        return view('item.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $location = ['Sisi A', 'Sisi B', 'Sisi C'];

        return view('item.edit', compact('item', 'location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string|required',
            'code' => 'string|nullable',
            'unit' => 'string|nullable',
            'location' => 'string|nullable',
            'min_stock' => 'integer|nullable',
            'stock' => 'integer|required',
        ]);

        $item->update($validatedData);

        return redirect()->route('item.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        $item->delete();

        return redirect()->route('item.index');
    }
}
