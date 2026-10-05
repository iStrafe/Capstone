<?php

namespace App\Http\Controllers;

use App\Enums\AdoptionStatus;
use App\Http\Requests\StoreAdoptionRequest;
use App\Http\Resources\AdoptionRequestResource;
use App\Http\Resources\CatResource;
use App\Models\Cat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdoptionController extends Controller
{
    // The signed-in applicant's own requests
    public function showMyRequests(Request $request): Response
    {
        $requests = $request->user()->adoptionRequests()
            ->with(['cat' => fn ($cat) => $cat->withExists([
                'adoptionRequests as is_reserved' => fn (Builder $requests) => $requests->whereIn('status', AdoptionStatus::reservingCat()),
            ])])
            ->latest('id')
            ->get();

        return Inertia::render('Requests/Mine', [
            'requests' => AdoptionRequestResource::collection($requests)->resolve(),
        ]);
    }

    // The adoption request page for one cat. Cats that can't take this request go back to their
    // profile, which explains why (already asked, reserved, full, archived...).
    public function start(Request $request, Cat $cat): Response|RedirectResponse
    {
        $user = $request->user();

        if ($cat->requestRefusalFor($user) !== null) {
            return redirect()->route('cats.show', $cat);
        }

        return Inertia::render('Adoption/Request', [
            'cat' => (new CatResource($cat))->resolve(),
            'applicant' => ['name' => $user->name, 'email' => $user->email],
            // The server's today, so the calendar agrees with the date rule in StoreAdoptionRequest.
            'today' => today()->toDateString(),
            'latestPickup' => StoreAdoptionRequest::latestPickup(),
        ]);
    }

    // Create adoption request
    public function create(StoreAdoptionRequest $request): RedirectResponse
    {
        $user = $request->user();

        $cat = DB::transaction(function () use ($request, $user) {
            // Lock the cat and check again, so a double submit or several applicants at once
            // can't slip past the one-open-request rule or the pending-request cap.
            $cat = Cat::lockForUpdate()->findOrFail($request->validated('cat_id'));
            if (($refusal = $cat->requestRefusalFor($user)) !== null) {
                throw ValidationException::withMessages(['cat_id' => $refusal]);
            }

            $valid_ids = [];
            foreach ($request->file('valid_id', []) as $file) {
                // IDs are personal documents: keep them off the public disk under a random name.
                $valid_ids[] = basename($file->store('valid-ids', config('filesystems.private_uploads_disk')));
            }

            $user->adoptionRequests()->create([
                'cat_id' => $cat->id,
                'name' => $request->validated('name'),
                'address' => $request->validated('address'),
                'email' => $request->validated('email'),
                'home_phone' => $request->validated('phone'),
                'mobile_phone' => $request->validated('phone'),
                // A copy of the cat's details at the time of the request, for the admin tables and PDFs.
                'name_of_cat' => $cat->cat_name,
                'breed' => $cat->breed,
                'approximate_age' => $cat->age,
                'sex' => strtolower($cat->sex),
                'color' => $cat->color,
                'date_of_adoption' => $request->validated('date_of_adoption'),
                'valid_id' => $valid_ids,
            ]);

            return $cat;
        });

        return redirect()->route('myRequest')
            ->with('success', 'Your request to adopt '.$cat->cat_name.' was sent. A volunteer will review it and the answer will show up here.');
    }
}
