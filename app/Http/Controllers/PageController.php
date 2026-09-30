<?php

namespace App\Http\Controllers;

use App\Http\Resources\CatResource;
use App\Models\Cat;
use Inertia\Inertia;
use Inertia\Response;

// The static public pages: About us and Contact.
class PageController extends Controller
{
    public function about(): Response
    {
        return Inertia::render('About', [
            // A few real campus cats with photos for the page's picture grid.
            'photos' => CatResource::collection(
                Cat::available()->whereNotNull('cat_image')->inRandomOrder()->limit(3)->get()
            )->resolve(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact');
    }
}
