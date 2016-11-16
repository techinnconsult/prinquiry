<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inquiry extends Model {

    //
    protected $table = 'inquiry';
    
    /*
     * $id: Seller Id
     * getInquiryBySupplierId: Return All Inquiries Which Received
    */
    public function getInquiriesBySupplierId($id) {
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'seller_inquiry.seller_id', '=', 'users.id')
                ->select('inquiry.*', 'seller_inquiry.status', 'seller_inquiry.delievery_date')
                ->where('seller_inquiry.seller_id', $id)
                ->get();
        return $Inquiries;
    }
    
    /*
     * $id: Seller Id
     * $inquiry_id: Inquiry Id
     * getInquiryBySupplierId: Return All Inquiries Which Received
    */
    public function getInquiryBySupplierId($id,$inquiry_id) {
        $Inquiry = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'seller_inquiry.seller_id', '=', 'users.id')
                ->select('inquiry.*', 'seller_inquiry.inquiry_details as seller_inquiry_details', 'seller_inquiry.seller_id', 'seller_inquiry.status', 'seller_inquiry.delievery_date')
                ->where('seller_inquiry.seller_id', $id)
                ->where('inquiry.id', $inquiry_id)
                ->first();
        return $Inquiry;
    }
    
    /*
     * $id: Supplier Id
     * getRecentInquiryBySupplierId: Return 5 recent inquiries which received
    */
    public function getRecentInquiriesBySupplierId($id) {
        
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'seller_inquiry.seller_id', '=', 'users.id')
                ->select('inquiry.*', 'seller_inquiry.status', 'seller_inquiry.delievery_date')
                ->where('seller_inquiry.seller_id', $id)
                ->whereRaw('seller_inquiry.status != "Reply"')
                 ->orderBy('inquiry.created_at', 'desc')
                ->limit(5)
                ->get();
        return $Inquiries;
    }
    
    /*
     * $id: Customer Id
     * getRecentCreatedInquiries: Return Recent Inquiries 
    */
    public function getRecentCreatedInquiries($id) {
        
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'inquiry.customer_id', '=', 'users.id')
                ->select('inquiry.*', 'seller_inquiry.status', 'seller_inquiry.delievery_date', DB::raw('count(*) as total'))
                ->where('inquiry.customer_id', $id)
                ->whereRaw('seller_inquiry.status != "Reply"')
                ->orderBy('inquiry.created_at', 'desc')
                ->groupBy('inquiry.id')
                ->limit(5)
                ->get();
        return $Inquiries;
    }
    
    public function getInquiriesSentByCustomerId($id) {
        
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'inquiry.customer_id', '=', 'users.id')
                ->select('inquiry.*', 'seller_inquiry.status', 'seller_inquiry.delievery_date','users.name')
                ->where('inquiry.customer_id', $id)
                ->orderBy('inquiry.created_at', 'desc')
                ->groupBy('inquiry.id')
                ->get();
        return $Inquiries;
    }
    
    public function getInquirySentByCustomerId($id,$inquiry_id) {
        
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'inquiry.customer_id', '=', 'users.id')
                ->select('inquiry.*', 'seller_inquiry.status', 'seller_inquiry.delievery_date','users.name')
                ->where('inquiry.customer_id', $id)
                ->where('inquiry.id', $inquiry_id)
                ->orderBy('inquiry.created_at', 'desc')
                ->groupBy('inquiry.id')
                ->first();
        return $Inquiries;
    }
    
    public function getSellerInquiryByInquiryId($inquiry_id,$status) {
        
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'seller_inquiry.seller_id', '=', 'users.id')
                ->select('seller_inquiry.*','users.name')
                ->where('inquiry.id', $inquiry_id)
                ->where('seller_inquiry.status', $status)
                ->orderBy('seller_inquiry.created_at', 'desc')
                ->get();
        return $Inquiries;
    }
    
    public function users(){
        return $this->hasOne('App\User','id');
    }

}
