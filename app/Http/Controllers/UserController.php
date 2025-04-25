<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Tambahkan ini untuk mengimport model User

class UserController extends Controller
{
    public function index()
    {
        $search = request('search');

        if ($search) {
            $users = User::where('id', '!=', 1)
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%");
                })
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString();
        } else {
            $users = User::where('id', '!=', 1)
                ->orderBy('name')
                ->paginate(10);
        }

        return view('user.index', compact('users'));
    }
}
