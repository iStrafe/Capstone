<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class adoptionController extends Controller
{
    public function showAdoptionForm(){
        return view('Services.adoptionForm');
    }

    //Show Pending and approved requests
    public function showAdoptionRequest(){
        // Each table pages on its own query-string key; sharing ?page moved all three at once.
        $adoption_request = DB::table('adoption_request')->paginate(10, ['*'], 'page');
        $approved_requests = DB::table('adoption_request')->where('status', 'Approved')->paginate(5, ['*'], 'approved_page');
        $rejected_request = DB::table('adoption_request')->where('status', 'Not Approved')->paginate(5, ['*'], 'rejected_page');
        return view('admin.adoptionRequest', [
            'adoption_request' => $adoption_request,
            'approved_requests' => $approved_requests,
            'rejected_request' => $rejected_request
        ]);
    }
    public function showReleased(){
        $released_request = DB::table('adoption_request')->where('status', 'Released')->paginate(5);
        return view('admin.released',['released_request' => $released_request]);
    }

    //Show only rejected requests
    //public function showRejected(){
       // $rejected_request = DB::table('adoption_request')->where('status', 'Not Approved')->paginate(5);
        //return view('admin.rejectedRequest',['rejected_request' => $rejected_request]);
    //}

    //Show all data
    public function showMyRequests() {
        $adoption_request = DB::table('adoption_request')->get();
        foreach ($adoption_request as $request) {
            $request->valid_id = json_decode($request->valid_id);
        }
        return view('myRequest', ['adoption_request' => $adoption_request]);
    }

    //Create adoption request
    public function create(Request $request){
        // The Adopt button pre-fills sex from the cat record ("Male"), but the column only accepts lowercase.
        if (is_string($request->input('sex'))) {
            $request->merge(['sex' => strtolower(trim($request->input('sex')))]);
        }

        // Rules mirror the adoption_request columns so bad input gets a message instead of a database error.
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'name_of_cat' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'approximate_age' => 'nullable|integer|min:0|max:40',
            'sex' => 'required|in:male,female',
            'color' => 'required|string|max:255',
            'date_of_adoption' => 'required|date',
            'valid_id' => 'nullable|array',
            'valid_id.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'email' => $request->input('email'),
            'home_phone' => $request->input('phone'),
            'mobile_phone' => $request->input('phone'),
            'name_of_cat' => $request->input('name_of_cat'),
            'breed' => $request->input('breed'),
            'approximate_age' => $request->input('approximate_age'),
            'sex' => $request->input('sex'),
            'color' => $request->input('color'),
            'date_of_adoption' => $request->input('date_of_adoption')
        ];

        $valid_ids = [];
        if ($request->hasFile('valid_id')) {
            foreach ($request->file('valid_id') as $file) {
                // IDs are personal documents: keep them off the public disk under a random name.
                $valid_ids[] = basename($file->store('valid-ids', 'local'));
            }
        }
        $data['valid_id'] = json_encode($valid_ids);

        DB::table('adoption_request')->insert($data);

        return redirect()->route('home')->with('success', 'Adoption request submitted successfully.');
        }

        //View valid ids
        public function viewValidIds($id)
        {
            $request = DB::table('adoption_request')->where('id', $id)->first();
            abort_if(! $request, 404);
            $valid_ids = json_decode($request->valid_id) ?: [];

            return view('viewValidIDs', compact('valid_ids'));
        }

        //Serve one uploaded ID to an admin
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

        //update request status
        public function updateStatus($id, Request $request){
            abort_unless(DB::table('adoption_request')->where('id', $id)->exists(), 404);

            // The admin table always sends these; the columns that are NOT NULL must not be blanked.
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'mobile_phone' => 'nullable|string|max:255',
                'name_of_cat' => 'required|string|max:255',
                'status' => 'required|string|max:255',
            ]);

            $data = [
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'mobile_phone' => $request->input('mobile_phone'),
                'name_of_cat' => $request->input('name_of_cat'),
                'status' => $request->input('status')
            ];

            if ($request->input('status') == 'Approved') {
                $data['approval_date'] = Carbon::now();
            }

             if ($request->input('status') == 'Released') {
                $data['Release_date'] = Carbon::now();
            }


            $result = DB::table('adoption_request')
                        ->where('id', $id)
                        ->update($data);

            if ($result) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update entry']);
            }
        }

        //Generate one row of the hool tebol
        public function generatePDF($id)
        {
            $request = DB::table('adoption_request')->where('id', $id)->first();
            abort_if(! $request, 404);

            $pdf = Pdf::loadView('adoptionRequestPDF', compact('request'));

            return $pdf->download('adoption_request.pdf');
        }

        //Generate the hool tebol
        public function generateAllPDF()
        {
            $requests = DB::table('adoption_request')->get();

            $pdf = Pdf::loadView('allAdoptionRequestPDF', compact('requests'));

            return $pdf->download('all_adoption_requests.pdf');
        }
    
}