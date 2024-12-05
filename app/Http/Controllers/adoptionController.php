<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class adoptionController extends Controller
{
    public function showAdoptionForm(){
        return view('Services.adoptionForm');
    }

    //Show Pending and approved requests
    public function showAdoptionRequest(){
        $adoption_request = DB::table('adoption_request')->paginate(5);
        $approved_requests = DB::table('adoption_request')->where('status', 'Approved')->paginate(5);
        $rejected_request = DB::table('adoption_request')->where('status', 'Not Approved')->paginate(5);
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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'valid_id.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images'), $filename);
                $valid_ids[] = $filename;
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
            $valid_ids = json_decode($request->valid_id);

            return view('viewValidIds', compact('valid_ids'));
        }

        //update request status
        public function updateStatus($id, Request $request){
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