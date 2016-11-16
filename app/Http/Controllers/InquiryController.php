<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\DB;

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
        $customer = User::with('inquiry')->get();
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
                ->get();
        $remaining_users = DB::table('users')
                ->whereNotIn('id', $not_in_array)
                 ->offset(10)
                 ->limit($users_count)
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
        for($i = 0;$i < count($inquiry_details_post)-2;){
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
        }
        $balance = $user->balance;
        DB::table('users')
                ->where('id',$user->id)
                ->update(['balance' => $balance-1]);
        
        $inquiry_id = DB::table('inquiry')->insertGetId(['inquir_details' => json_encode($inquiry_details),
            'customer_id' => $user->id, 'priority' => $request->priority,
            'delivery_required' => $request->delivery_required, 'location' => $request->location, 'created_at' => date('Y-m-d H:i:s')]);
        
        $seller_ids = $_POST['inquiry-supplier'];
        foreach($seller_ids as $seller_id){
            $seller_inquiry = array();
            DB::table('seller_inquiry')->insert(['seller_id' => $seller_id,'inquiry_id' => $inquiry_id,'status' => 'New','created_at' => date('Y-m-d H:i:s')]);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
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
        return view('inquiry.received',['inquiries' => $inq->getInquiriesBySupplierId($input['seller_id']),'customers' => $customer]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
