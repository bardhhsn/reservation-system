<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationChangeRequestController extends Controller
{
    public function index()
    {
        // Vetëm admini sheh kërkesat
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return ReservationChangeRequest::with('reservation', 'user')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'type' => 'required|in:edit,delete',
            'new_data' => 'nullable|array'
        ]);

        return ReservationChangeRequest::create([
            'reservation_id' => $request->reservation_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'new_data' => $request->new_data,
            'status' => 'pending'
        ]);
    }

    public function approve($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $req = ReservationChangeRequest::findOrFail($id);
        $reservation = $req->reservation;

        if ($req->type === 'edit' && $req->new_data) {
            $reservation->update($req->new_data);
        } elseif ($req->type === 'delete') {
            $reservation->delete();
        }

        $req->status = 'approved';
        $req->save();

        return response()->json(['message' => 'Kërkesa u aprovua.']);
    }

    public function reject($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $req = ReservationChangeRequest::findOrFail($id);
        $req->status = 'rejected';
        $req->save();

        return response()->json(['message' => 'Kërkesa u refuzua.']);
    }
}
