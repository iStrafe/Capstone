<?php

namespace App\Http\Controllers;

use App\Models\Cat;
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

    public function index(): View
    {
        return view('cats.index', ['cats' => Cat::available()->get()]);
    }

    public function show(Cat $cat): View
    {
        return view('cats.show', compact('cat'));
    }
}
