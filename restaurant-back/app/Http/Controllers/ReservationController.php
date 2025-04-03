<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return Reservation::with('table')->get();
    }

    public function store(Request $request)
{
    $request->validate([
        'table_id' => 'required|exists:tables,id',
        'customer_name' => 'required|string',
        'customer_phone' => 'required|string',
        'reservation_time' => 'required|date',
        'guest_count' => 'required|integer|min:1',
    ]);

    $requestedTime = \Carbon\Carbon::parse($request->reservation_time);

    // Kontrolli për numrin e personave
    $table = \App\Models\Table::findOrFail($request->table_id);
    if ($request->guest_count > $table->seats) {
        return response()->json([
            'message' => "Tavolina e zgjedhur ka vetëm {$table->seats} vende dhe nuk mund të strehojë {$request->guest_count} persona."
        ], 409);
    }

    // Kontrolli për konflikt në kohë
    $start = $requestedTime->copy()->subHours(2)->subMinutes(59);
    $end = $requestedTime->copy()->addHours(2)->addMinutes(59);

    $conflictingReservation = \App\Models\Reservation::where('table_id', $request->table_id)
        ->whereBetween('reservation_time', [$start, $end])
        ->first();

    if ($conflictingReservation) {
        $freeTime = \Carbon\Carbon::parse($conflictingReservation->reservation_time)
                    ->addHours(2)->addMinutes(59)
                    ->format('H:i');

        return response()->json([
            'message' => "Kjo tavolinë është e rezervuar deri në orën $freeTime."
        ], 409);
    }

    return Reservation::create($request->all());
}



    public function show($id)
    {
        return Reservation::with('table')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'reservation_time' => 'required|date',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());

        return $reservation;
    }

    public function destroy($id)
    {
        Reservation::destroy($id);
        return response()->json(['message' => 'Rezervimi u fshi me sukses']);
    }
}
