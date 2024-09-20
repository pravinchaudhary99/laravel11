<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{

   public static function middleware(): array
    {
        return [
            new Middleware('permissions:role-list,role-create,role-edit', only: ['index']),
        ];
    }

    public function index() {
        return view('roles.index');
    }

    public function create() {
        return view('roles.index');
    }
}
