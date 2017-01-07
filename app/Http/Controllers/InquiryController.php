<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\Role;
use App\User;
use App\Inquiry;
use Illuminate\Auth\GenericUser;

class InquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();
        $inq = new Inquiry();
        return view('inquiry.index',['inquiries' => $inq->getInquiriesSentByCustomerId($user->id)]);  
    }
    
    public function received()
    {
        $user = auth()->user();
        $inq = new Inquiry();
        $customer = User::with('inquiry')->get();
        return view('inquiry.received',['inquiries' => $inq->getInquiriesBySupplierId($user->id),'customers' => $customer]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = DB::table('category')->orderBy('name', 'asc')->get();
        $user = auth()->user();
        
        $selected_customers = DB::table('users')
            ->leftJoin('customer_seller', 'customer_seller.seller_id', '=', 'users.id')
            ->select('users.*','customer_seller.seller_id')
            ->whereRaw('customer_seller.customer_id = '.$user->id)
            ->orderBy('users.name', 'asc')
            ->get();
        $not_in_array = array();
        foreach($selected_customers as $selected_customer){
            $not_in_array[] = $selected_customer->seller_id;
        }
        $not_in_array[] = $user->id;
        
        $users_count = DB::table('users')
                ->whereNotIn('id', $not_in_array)->count();
//        $users_not_in = implode(',',$not_in_array);
        $users = DB::table('users')
                ->whereNotIn('id', $not_in_array)
                 ->limit(10)
                ->orderBy('users.name', 'asc')
                ->get();
        $remaining_users = DB::table('users')
                ->whereNotIn('id', $not_in_array)
                 ->offset(10)
                 ->limit($users_count)
                ->orderBy('users.name', 'asc')
                ->get();
        return view('inquiry.create', ['remaining_users' => $remaining_users,'categories' => $categories,'users' => $users,'selected_customers' => $selected_customers]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $inquiry_details = array();
        $inquiry_details_post = $request->inqpost;
        $index = 0;
        /*
         * Create Inquiry Details Json
         */
        for($i = 0;$i < count($inquiry_details_post)-2;){
            if($inquiry_details_post[$i]['partnum'] != ''){           
                for($j = 0; $j < 6; $j++){
                    if(isset($inquiry_details_post[$i])){
                        $value = $inquiry_details_post[$i];
                        if (isset($value['partnum'])){
                            $inquiry_details[$index][$j]['partnum'] = $value['partnum'];
                        }elseif(isset($value['qty'])){
                            $inquiry_details[$index][$j]['qty'] = $value['qty'];
                        }elseif(isset($value['unit'])){   
                            $inquiry_details[$index][$j]['unit'] = $value['unit'];
                        }elseif(isset($value['type'])){   
                            $inquiry_details[$index][$j]['type'] = $value['type'];
                        }elseif(isset($value['category'])){
                            $inquiry_details[$index][$j]['category'] = $value['category'];
                        }elseif(isset($value['detail'])){
                            $inquiry_details[$index][$j]['detail'] = $value['detail'];
                        }
                    }else{
                        break;
                    }
                    $i = $i+1;
                }
                $index = $index+1;
            }else{
                $i = $i+6;
            }
        }
        
        /*
         * Deduct Customer Blanace of creating Inquiry
         */
        $balance = $user->balance;
        DB::table('users')
                ->where('id',$user->id)
                ->update(['balance' => $balance-1]);
        
        /*
         * Create Inquiry
         */
        $inquiry_id = DB::table('inquiry')->insertGetId(['inquir_details' => json_encode($inquiry_details),
            'customer_id' => $user->id, 'priority' => $request->priority,
            'delivery_required' => $request->delivery_required, 'location' => $request->location, 'created_at' => date('Y-m-d H:i:s')]);
        
        /*
         * Add Sellers to that particular Inquiry and send them email with inquiry overview. 
         */
        
        $seller_ids = $_POST['inquiry-supplier'];
        foreach($seller_ids as $seller_id){
            $seller = User::where('id',$seller_id) -> first();
            /*
             * Add suppliers in preffered list. 
             * If they invite them previously by same customer then they will be added in preferred supplier list 
             */
            $seller_count = DB::table('inquiry as i')
                ->leftJoin('seller_inquiry as si', 'i.id', '=', 'si.inquiry_id')
                ->where('i.customer_id', $user->id)
                ->where('si.seller_id', $seller_id)
                ->where('i.closed', '0')
                ->count();
            if($seller_count > 2){
                $preffered_user_count = DB::table('customer_seller')
                ->where('customer_id', $user->id)
                ->where('seller_id', $seller_id)
                ->count();
                if($preffered_user_count == 0){
                    DB::table('customer_seller')->insert(['seller_id' => $seller_id,'customer_id' => $user->id,'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')]);
                }
            }
            $seller_inquiry = array();
            /*
             * Add suppliers to that particular inquiry
             */
            DB::table('seller_inquiry')->insert(['seller_id' => $seller_id,'inquiry_id' => $inquiry_id,'status' => 'New','created_at' => date('Y-m-d H:i:s')]);
            $code['seller_name'] = $seller->name;
            $code['user_name'] = $user->name;
            $code['location'] = $request->location;
            $code['priority'] = $request->priority;
            $code['inquiry_id'] = $inquiry_id;
            $code['inquiry_details'] = json_encode($inquiry_details);
            $data['to'] = $seller->email;
            $data['seller_name'] = $seller->name;
            $data['user_name'] = $user->name;
            Mail::send('emails.supplier', $code, function($message) use ($data) {
                $message->to($data['to'] , $data['seller_name'])
                    ->subject('New Inquiry Created by '.$data['user_name']);
            });
        }
        return redirect()->route('inquiry.index')->withMessage(trans('Inquiry Created Successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $inq = new Inquiry();
        $customer = User::with('inquiry')->get();
        return view('inquiry.reply',['inquiries' => $inq->getInquiryBySupplierId($user->id,$id),'customers' => $customer]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $inquiry_id
     * @return Response
     */
    public function reply($inquiry_id)
    {
        $user = auth()->user();
        $inq = new Inquiry();
        
        DB::table('seller_inquiry')
            ->where('seller_id', $user->id)
            ->where('inquiry_id', $inquiry_id)
            ->where('status', 'New')
            ->update([ 'status' => 'Pending', 
                'updated_at' => date('Y-m-d H:i:s')]);
        
        $customer = User::with('inquiry')->get();
        return view('inquiry.reply',['inquiries' => $inq->getInquiryBySupplierId($user->id,$inquiry_id),'customers' => $customer]);
    }
    
    /**
     * 
     * Display the specified resource.
     * @param int $inquiry_id
     */
    public function details($inquiry_id) {
        $user = auth()->user();
        $inq = new Inquiry();
        $customer = User::with('inquiry')->get();
        return view('inquiry.details',['inquiries' => $inq->getInquirySentByCustomerId($user->id,$inquiry_id),'seller_inquiry' => $inq->getSellerInquiryByInquiryId($inquiry_id,'Reply'),'customers' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        $input = $request->all();
        DB::table('seller_inquiry')
            ->where('seller_id', $input['seller_id'])
            ->where('inquiry_id', $input['id'])
            ->update(['delievery_date' => $input['delievery_date'], 'status' => 'Reply', 
                'updated_at' => date('Y-m-d H:i:s'), 'inquiry_details' => json_encode($input['inqpost'])]);
        $user = auth()->user();
        $balance = $user->balance;
        DB::table('users')
                ->where('id',$user->id)
                ->update(['balance' => $balance-1]);
        $inq = new Inquiry();
        
        $customer = User::with('inquiry')->get();
        
        $user_to = User::where('id',$input['customer_id']) -> first();
        $data['to'] = $user_to->email;
        $data['user_name'] = $user_to->name;
        $data['user_name'] = $user_to->name;
        $data['seller_name'] = $user->name;
        $code['user_name'] =  $user_to->name;
        $code['seller_name'] = $user->name;
        $code['inquiry_id'] =$input['id'];
        Mail::send('emails.reply', $code, function($message) use ($data) {
                $message->to($data['to'] , $data['user_name'])
                    ->subject('Inquiry Replied by '.$data['seller_name']);
            });
        return view('inquiry.received',['inquiries' => $inq->getInquiriesBySupplierId($input['seller_id']),'customers' => $customer]);
    }
    
    public function supplier($inquiry_id){
        $inq = new Inquiry();
        return json_encode($inq->getSellersDetailsByInquiryId($inquiry_id));
    }
    
    public function suppliers(){
         $user = auth()->user();
        
        $selected_customers = DB::table('users')
            ->leftJoin('customer_seller', 'customer_seller.seller_id', '=', 'users.id')
            ->select('users.*','customer_seller.seller_id')
            ->whereRaw('customer_seller.customer_id = '.$user->id)
            ->orderBy('users.name', 'asc')
            ->get();
        $not_in_array = array();
        foreach($selected_customers as $selected_customer){
            $not_in_array[] = $selected_customer->seller_id;
        }
        $not_in_array[] = $user->id;
        
        $users_count = DB::table('users')
                ->whereNotIn('id', $not_in_array)->count();
        $users = DB::table('users')
                ->whereNotIn('id', $not_in_array)
                ->orderBy('users.name', 'asc')
                ->get();
        return response($users);
    }
    public function shortView($inquiry_id){
        $inq = new Inquiry();
        $inquiry = $inq->getInquiryById($inquiry_id);
        $inquiry_details = json_decode($inquiry->inquir_details,true);
        $details = array();
        $count = 5;
        if(count($inquiry_details) < 5){
            $count = count($inquiry_details);
        }
        for($i = 0;$i<$count;$i++){
            $inquiry_detail = $inquiry_details[$i];
            $details[$i]['partnum'] = $inquiry_detail[0]['partnum'];
            $details[$i]['qty'] = $inquiry_detail[1]['qty'];
            $details[$i]['unit'] = $inquiry_detail[2]['unit'];
            $details[$i]['type'] = $inquiry_detail[3]['type'];
            $details[$i]['category'] = $inquiry_detail[4]['category'];
            $details[$i]['detail'] = $inquiry_detail[5]['detail'];
        }
        return json_encode($details);
    }
    
    public function closeSellerInquiry($id)
    {
        $user = auth()->user();
        
        DB::table('seller_inquiry')
            ->where('seller_id', $user->id)
            ->where('inquiry_id', $id)
            ->where('closed', '0')
            ->update([ 'closed' => '1', 
                'updated_at' => date('Y-m-d H:i:s')]);
        
        $inq = new Inquiry();
        $customer = User::with('inquiry')->get();
        return view('inquiry.received',['inquiries' => $inq->getInquiriesBySupplierId($user->id),'customers' => $customer]);
    }
    
    public function deleteSellerInquiry($id)
    {
        $user = auth()->user();
        
        DB::table('seller_inquiry')
            ->where('seller_id', $user->id)
            ->where('inquiry_id', $id)
            ->delete();
        
        $inq = new Inquiry();
        $customer = User::with('inquiry')->get();
        return view('inquiry.received',['inquiries' => $inq->getInquiriesBySupplierId($user->id),'customers' => $customer]);
    } 
    
    public function closeInquiry($id)
    {
        $user = auth()->user();
        $inq = new Inquiry();
        return view('inquiry.index',['inquiries' => $inq->getInquiriesSentByCustomerId($user->id)]);  
    }
    
    public function deleteInquiry($id)
    {
        $user = auth()->user();
        $inq = new Inquiry();
        
        $inq->deleteInquiry($id,$user->id);
        
        return view('inquiry.index',['inquiries' => $inq->getInquiriesSentByCustomerId($user->id)]);  
    }
}
