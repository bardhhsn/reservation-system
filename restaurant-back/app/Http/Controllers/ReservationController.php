<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return Reservation::with(['table', 'user'])->get();
        }

        return Reservation::with('table')
            ->where('user_id', Auth::id())
            ->get();
    }

    public function store(Request $request)
    {
        // wrap the whole thing so we see any exception
        try {
            $data = $request->validate([
                'table_id'         => 'required|exists:tables,id',
                'customer_name'    => 'required|string',
                'customer_phone'   => 'required|regex:/^[0-9]+$/',
                'reservation_time' => 'required|date',
                'guest_count'      => 'required|integer|min:1',
            ]);

            $requestedTime = \Carbon\Carbon::parse($data['reservation_time']);
            $table = Table::findOrFail($data['table_id']);

            if ($data['guest_count'] > $table->seats) {
                return response()->json([
                    'message' => "Tavolina e zgjedhur ka vetëm {$table->seats} vende dhe nuk mund të strehojë {$data['guest_count']} persona."
                ], 409);
            }

            $start = $requestedTime->copy()->subHours(2)->subMinutes(59);
            $end   = $requestedTime->copy()->addHours(2)->addMinutes(59);

            $conflict = Reservation::where('table_id', $data['table_id'])
                ->whereBetween('reservation_time', [$start, $end])
                ->first();

            if ($conflict) {
                $freeTime = \Carbon\Carbon::parse($conflict->reservation_time)
                    ->addHours(2)->addMinutes(59)
                    ->format('H:i');

                return response()->json([
                    'message' => "Kjo tavolinë është e rezervuar deri në orën $freeTime."
                ], 409);
            }

            $data['user_id'] = Auth::id();
            $reservation = Reservation::create($data);

            return response()->json($reservation, 201);

        } catch (\Throwable $e) {
            // log and return the real error message for debugging
            \Log::error('ReservationController@store failed: '.$e->getMessage());
            return response()->json([
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $reservation = Reservation::with('table')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Nuk ke akses te kjo rezervim'], 403);
        }

        return response()->json($reservation, 200);
    }

    public function update(Request $request, $id)
    {
        // ...same wrap-in-try/catch if you like for update as well...
    }

    public function destroy($id)
    {
        // ...and for destroy...
    }
}
