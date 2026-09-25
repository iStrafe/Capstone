<?php

namespace App\Http\Controllers;

use App\Enums\AdoptionStatus;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public cat pages. Admin inventory management lives in Admin\CatController.
 */
class CatController extends Controller
{
    public function home(): View
    {
        return view('home', ['cats' => Cat::available()->get()]);
    }

    public function dashboard(): View
    {
        return view('dashboard', ['cats' => Cat::available()->get()]);
    }

    public function index(Request $request): View
    {
        return view('cats.index', [
            'cats' => Cat::available()->withPendingRequestCount()->get(),
            // Cats the signed-in user is already waiting on, so their Adopt button can say so.
            'requestedCatIds' => $request->user()?->adoptionRequests()
                ->whereIn('status', AdoptionStatus::open())
                ->pluck('cat_id')
                ->all() ?? [],
        ]);
    }

    public function show(Cat $cat): View
    {
        return view('cats.show', compact('cat'));
    }
}
