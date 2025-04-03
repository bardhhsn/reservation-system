<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return Table::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'seats' => 'required|integer',
        ]);

        return Table::create($request->all());
    }

    public function show($id)
    {
        return Table::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $table->update($request->all());
        return $table;
    }

    public function destroy($id)
    {
        Table::destroy($id);
        return response()->json(['message' => 'Tavolina u fshi me sukses']);
    }
}
