<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apoio;
use App\Models\Escola;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApoioController extends Controller
{
    public function index(){
        return view('Apoio.index');
    }
}
