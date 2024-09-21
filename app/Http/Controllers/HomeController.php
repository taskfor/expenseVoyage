<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home(){
        if(Auth::check()){
            $role = Auth::user()->role;
            if($role == 'admin'){
                return view('admin.dashboard');
            } else if($role == 'user'){
                return view('user.dashboard');
            }

        }
        return redirect()->route('login');
    }
    public function destination (){
        $destinations = Destination::all();
        return view('ExpenseVoyage.index', compact('destinations'));
    }
}
