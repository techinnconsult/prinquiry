<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\DB;

use App\Role;
use App\User;
use App\Inquiry;
use Illuminate\Auth\GenericUser;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $inq = new Inquiry();
        $customer = User::with('inquiry')->get();
        return view('home',['sentInquiry' => $inq->getRecentCreatedInquiries($user->id),'inquiries' => $inq->getRecentInquiriesBySupplierId($user->id),'customers' => $customer]);
    }
}
