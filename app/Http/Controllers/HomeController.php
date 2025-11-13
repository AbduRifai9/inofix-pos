<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
    
    public function pos()
    {
        return view('pos');
    }
    
    public function products()
    {
        return view('products.index');
    }
    
    public function customers()
    {
        return view('customers.index');
    }
    
    public function transactions()
    {
        return view('transactions.index');
    }
}