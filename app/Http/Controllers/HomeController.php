<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\DB;

use App\Role;
use App\User;
use App\Inquiry;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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
        $selected_customers = DB::table('users')
            ->leftJoin('customer_seller', 'customer_seller.seller_id', '=', 'users.id')
            ->select('users.*','customer_seller.seller_id')
            ->whereRaw('customer_seller.customer_id = '.$user->id)
            ->orderBy('users.name', 'asc')
            ->limit(5)
            ->get();
        return view('home',['selected_customers'=>$selected_customers,'sentInquiry' => $inq->getRecentCreatedInquiries($user->id),'inquiries' => $inq->getRecentInquiriesBySupplierId($user->id),'customers' => $customer]);
    }
    
    public function contactus(Request $request){
        $input = $request->all();
        $admin_users = User::where('role_id','1') -> get();
        foreach($admin_users as $admin_user){
            $data['to'] = $admin_user->email;
            $data['name'] = $admin_user->name;
            $code['user_name'] = $input['name'];
            $code['user_email'] = $input['email'];
            $code['user_message'] = $input['message'];
            Mail::send('emails.contactus', $code, function($message) use ($data) {
                $message->to($data['to'] , $data['name'])
                    ->subject($data['name'].' Contacts you from Autoparts Inquiry');
            });
        }
        $user = auth()->user();
        $inq = new Inquiry();
        $customer = User::with('inquiry')->get();
        $selected_customers = DB::table('users')
            ->leftJoin('customer_seller', 'customer_seller.seller_id', '=', 'users.id')
            ->select('users.*','customer_seller.seller_id')
            ->whereRaw('customer_seller.customer_id = '.$user->id)
            ->orderBy('users.name', 'asc')
            ->limit(5)
            ->get();
        return view('home',['selected_customers'=>$selected_customers,'sentInquiry' => $inq->getRecentCreatedInquiries($user->id),'inquiries' => $inq->getRecentInquiriesBySupplierId($user->id),'customers' => $customer]);
    }
}
