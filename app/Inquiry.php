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
                ->orderByRaw("FIELD(seller_inquiry.status , 'New','Pending','Reply') ASC")
                ->orderBy('inquiry.id','desc')
                ->where('seller_inquiry.seller_id', $id)
                ->where('seller_inquiry.closed', '0')
                ->get();
        return $Inquiries;
    }
    /*
     * $id: Inquiry Id
     * getInquiryBySupplierId: Return All Inquiries Which Received
    */
    public function getInquiryById($id) {
        $Inquiries = DB::table('inquiry')
                ->where('inquiry.id', $id)
                ->where('inquiry.closed', '0')
                ->first();
        return $Inquiries;
    }
    public function sellerInquiries()
    {
        return $this->hasMany('App\SellerInquiry');
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
                ->where('inquiry.closed', '0')
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
                ->where('inquiry.closed', '0')
                ->orderByRaw("FIELD(seller_inquiry.status , 'New','Pending','Reply') ASC")
                ->orderBy('inquiry.id','desc')
                ->limit(5)
                ->get();
        return $Inquiries;
    }
    
    /*
     * $id: Customer Id
     * getRecentCreatedInquiries: Return Recent Inquiries 
    */
    public function getRecentCreatedInquiries($id) {
        $countReply = new \Sofa\Eloquence\Subquery(
            DB::table('seller_inquiry')
                ->selectRaw('count(seller_inquiry.id)')
                ->whereRaw('seller_inquiry.inquiry_id=i.id')
                ->whereRaw('seller_inquiry.status="Reply"'), 
            'count_reply' // alias
        );
        
        $Inquiries = DB::table('inquiry as i')
                ->leftJoin('users', 'i.customer_id', '=', 'users.id')
                ->select('i.*', $countReply)
                ->where('i.customer_id', $id)
                ->where('i.closed', '0')
                ->addBinding($countReply->getBindings(), 'select')
                ->orderBy('i.created_at', 'desc')
                ->groupBy('i.id')
                ->limit(5)
                ->get();
        return $Inquiries;
    }
    /*
     * $id: Customer Id
     * getInquiriesSentByCustomerId: Return Inquiries which Customer sent to suppliers
    */
    public function getInquiriesSentByCustomerId($id) {
        DB::enableQueryLog();
        $countReply = new \Sofa\Eloquence\Subquery(
            DB::table('seller_inquiry')
                ->selectRaw('count(seller_inquiry.id)')
                ->whereRaw('seller_inquiry.inquiry_id=i.id')
                ->whereRaw('seller_inquiry.status="Reply"'), 
            'count_reply' // alias
        );
        
        $Inquiries = DB::table('inquiry as i')
                ->leftJoin('users', 'i.customer_id', '=', 'users.id')
                ->select('i.*','users.name',$countReply)
                ->addBinding($countReply->getBindings(), 'select')
                ->where('i.customer_id', $id)
                ->where('i.closed', '0')
                ->orderBy('i.created_at', 'desc')
                ->get();
        return $Inquiries;
    }
    /*
     * $id: Customer Id
     * $inquiry_id: Inquiry Id
     * getInquirySentByCustomerId: Return Single Inquiry to view Inquiry Details Page
     * 
    */
    public function getInquirySentByCustomerId($id,$inquiry_id) {
        
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'inquiry.customer_id', '=', 'users.id')
                ->select('inquiry.*', 'seller_inquiry.status', 'seller_inquiry.delievery_date','users.name')
                ->where('inquiry.customer_id', $id)
                ->where('inquiry.id', $inquiry_id)
                ->where('inquiry.closed', '0')
                ->orderBy('inquiry.created_at', 'desc')
                ->groupBy('inquiry.id')
                ->first();
        return $Inquiries;
    }
    
    /*
     * $inquiry_id: Inquiry Id
     * $status: Status could be New, Pending or Replied
     * getSellerInquiryByInquiryId: Return Seller Inquiries
     * 
    */
    
    public function getSellerInquiryByInquiryId($inquiry_id,$status) {
        
        $Inquiries = DB::table('inquiry')
                ->leftJoin('seller_inquiry', 'inquiry.id', '=', 'seller_inquiry.inquiry_id')
                ->leftJoin('users', 'seller_inquiry.seller_id', '=', 'users.id')
                ->select('seller_inquiry.*','users.name')
                ->where('inquiry.id', $inquiry_id)
                ->where('seller_inquiry.status', $status)
                ->where('inquiry.closed', '0')
                ->orderBy('seller_inquiry.created_at', 'desc')
                ->get();
        return $Inquiries;
    }
    
    public function getSellersDetailsByInquiryId($inquiry_id) {
        
        $Sellers = DB::table('seller_inquiry')
                ->leftJoin('users', 'seller_inquiry.seller_id', '=', 'users.id')
                ->select('seller_inquiry.*','users.name')
                ->where('seller_inquiry.inquiry_id', $inquiry_id)
                ->where('seller_inquiry.closed', '0')
                ->orderBy('seller_inquiry.created_at', 'desc')
                ->get();
        return $Sellers;
    }
    
    /*
     * $inquiry_id: Inquiry Id
     * $user_id: User Id
     * deleteInquiry: First delete all supplier inquiries and then delete that particular incuiries by customer
     * 
     */
    
    public function deleteInquiry($inquiry_id, $user_id){
        DB::table('seller_inquiry')
            ->where('inquiry_id', $inquiry_id)
            ->delete();
        
        DB::table('inquiry')
            ->where('customer_id', $user_id)
            ->where('id', $inquiry_id)
            ->delete();
    }
    
    /*
     * $inquiry_id: Inquiry Id
     * $user_id: User Id
     * closeInquiry: First close all supplier inquiries and then close that particular incuiries by customer
     * 
     */
    
    public function closeInquiry($inquiry_id,$user_id) {
        DB::table('seller_inquiry')
            ->where('inquiry_id', $inquiry_id)
            ->where('closed', '0')
            ->update([ 'closed' => '1', 
                'updated_at' => date('Y-m-d H:i:s')]);
        
        DB::table('inquiry')
            ->where('customer_id', $user_id)
            ->where('id', $inquiry_id)
            ->where('closed', '0')
            ->update([ 'closed' => '1', 
                'updated_at' => date('Y-m-d H:i:s')]);
    }
    public function users(){
        return $this->hasOne('App\User','id');
    }

}
