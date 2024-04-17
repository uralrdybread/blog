<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function homepage() {

        $ourName = 'Doug';
        $animals = ['Mewoalot', 'Barasalot', 'Purrsloud'];

        return view('homepage', ['allAnimals' => $animals, 'name' => $ourName, 'catman' => 'Meowsalot']);
    }

    public function aboutPage() {
        return view('single-post');
    }
}
