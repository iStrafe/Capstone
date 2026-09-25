<?php

namespace App\Http\Controllers;

use App\Enums\AdoptionStatus;
use App\Http\Requests\Admin\UpdateAdoptionStatusRequest;
use App\Http\Requests\StoreAdoptionRequest;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdoptionController extends Controller
{
    // Show all, approved and rejected requests
    public function showAdoptionRequest(): View
    {
        // Each table pages on its own query-string key; sharing ?page moved all three at once.
        return view('admin.adoptionRequest', [
            'adoption_request' => AdoptionRequest::latest('id')->paginate(10, ['*'], 'page'),
            'approved_requests' => AdoptionRequest::where('status', AdoptionStatus::Approved)->latest('id')->paginate(5, ['*'], 'approved_page'),
            'rejected_request' => AdoptionRequest::where('status', AdoptionStatus::Rejected)->latest('id')->paginate(5, ['*'], 'rejected_page'),
        ]);
    }

    public function showReleased(): View
    {
        return view('admin.released', [
            'released_request' => AdoptionRequest::where('status', AdoptionStatus::Released)->latest('id')->paginate(5),
        ]);
    }

    // The signed-in applicant's own requests
    public function showMyRequests(Request $request): View
    {
        return view('myRequest', [
            'adoption_request' => $request->user()->adoptionRequests()->with('cat')->latest('id')->get(),
        ]);
    }

    // Create adoption request
    public function create(StoreAdoptionRequest $request): RedirectResponse
    {
        $cat = Cat::findOrFail($request->validated('cat_id'));

        $valid_ids = [];
        foreach ($request->file('valid_id', []) as $file) {
            // IDs are personal documents: keep them off the public disk under a random name.
            $valid_ids[] = basename($file->store('valid-ids', 'local'));
        }

        $request->user()->adoptionRequests()->create([
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

        return redirect()->route('myRequest')->with('success', 'Adoption request submitted successfully.');
    }

    // View valid ids
    public function viewValidIds(AdoptionRequest $adoptionRequest): View
    {
        return view('viewValidIDs', ['valid_ids' => $adoptionRequest->valid_id ?: []]);
    }

    // Serve one uploaded ID to an admin
    public function showValidIdFile(string $filename)
    {
        $filename = basename($filename);

        if (Storage::disk('local')->exists('valid-ids/'.$filename)) {
            return Storage::disk('local')->response('valid-ids/'.$filename);
        }

        // IDs uploaded before they moved to private storage still live in public/images.
        $legacy = public_path('images/'.$filename);
        abort_unless(is_file($legacy), 404);

        return response()->file($legacy);
    }

    // Update request status (admins change the status only; applicant details stay as submitted)
    public function updateStatus(UpdateAdoptionStatusRequest $request, AdoptionRequest $adoptionRequest): JsonResponse
    {
        $status = $request->enum('status', AdoptionStatus::class);

        $adoptionRequest->status = $status;
        if ($status === AdoptionStatus::Approved) {
            $adoptionRequest->approval_date = now();
        }
        if ($status === AdoptionStatus::Released) {
            $adoptionRequest->Release_date = now();
        }
        $adoptionRequest->save();

        return response()->json(['success' => true]);
    }

    // Download one request as PDF
    public function generatePDF(AdoptionRequest $adoptionRequest)
    {
        $pdf = Pdf::loadView('adoptionRequestPDF', ['request' => $adoptionRequest]);

        return $pdf->download('adoption_request.pdf');
    }
}
