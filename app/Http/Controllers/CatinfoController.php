<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatInfo;
use Illuminate\Support\Facades\DB;

class CatinfoController extends Controller
{
    public function index(){
        //$data = DB::all();
       // $displayData = DB::table('catinfo')->get();
      //  return view('admin.create',['displayData' => $displayData]);
       //  $displayData = DB::all();
        //return view('admin.index',['displayData' => $displayData]);


        $select = DB::select('select * from catinfo');
        
    }

    public function create(){
        return view('admin.create');
    }

     public function HomePageRoute(){
        return view('dashboard');
    }

    

    // =============== Add Cat to Database ==============
    public function store(Request $request){
        //dd($request);
        $data = [
                    'name' => $request->input('name'),
                    'gender' => $request->input('gender'),
                    'breed' => $request->input('breed'),
                    'eye_color' => $request->input('eye_color'),
                    'fur_color' => $request->input('fur_color'),
                    'description' => $request->input('description'),
                    'cat_image' => $request->input('cat_image'),
                    'status' => $request->input('status')
                ];

        $result = DB::table('catinfo')->insert($data);

        return redirect()->route('admin.store');
    }

    public function search(Request $request) {
    $query = $request->input('query');

    $displayData = DB::table('catinfo')
                    ->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('gender', 'LIKE', "%{$query}%")
                    ->orWhere('breed', 'LIKE', "%{$query}%")
                    ->orWhere('eye_color', 'LIKE', "%{$query}%")
                    ->orWhere('fur_color', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->get();

    return view('admin.create', compact('displayData'));
}


    //======= Display data in Homepage and Adminpage =======
    // Note: This section is for displaying information from the database. 
    public function viewCatInformation(){
        $displayData = DB::table('catinfo')->get();
        return view('dashboard',compact('displayData'));
    }

    public function viewCatInformation2(){
        $displayData = DB::table('catinfo')->get();
      //  $displayreportdata = DB::table('reportcat')->get();
        return view('admin.create',compact('displayData'));
    }
    
    // ============ Report page route =============
     public function reportpage(){
        return view('Services.report');
    }
    //=========== Display report data in Admin page
   /* public function viewReportInformation(){
        $displayreportdata = DB::table('reportcat')->get();
        return view('admin.create',compact('displayreportdata'));
    }*/


        //============ Report Cat form ==============
      public function report(Request $request){
        //dd($request);
        $data = [
                    'name' => $request->input('name'),
                    'gender' => $request->input('gender'),
                    'breed' => $request->input('breed'),
                    'eye_color' => $request->input('eye_color'),
                    'fur_color' => $request->input('fur_color'),
                    'last_seen_date' => date('Y-m-d H:i:s'),
                    'last_seen_location' => $request->input('last_seen_location'),
                    'contact_email' => $request->input('contact_email'),
                    'catimage' => $request->input('catimage')
                ];

        $result = DB::table('reportcat')->insert($data);

        return redirect()->route('admin.report');
    }

    public function deleteInfo(Request $request){
        
    }
}
