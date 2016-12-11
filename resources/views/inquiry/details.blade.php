@extends('layouts.app')

@section('content')
    <h2>Inquiry Details</h2>
    <div class="row uniform">
        <div class="12u$">
            <div class="split style6">
                <section style="border:none;">
                    <label for="id">Inquiry #</label>
                    {{ $inquiries->id }}
                </section>
                <section style="border: medium none; padding-left: 0px; padding-right: 26px;">
                    <label for="created_at">Date</label>
                   {{ Carbon\Carbon::parse($inquiries->created_at)->format('Y-m-d') }}
                </section>
                <section style="border:none;">
                    <label for="id">Customer</label>
                    @foreach($customers as $customer)
                        @if($customer->id == $inquiries->customer_id)
                       {{ $customer->name }}
                       @endif
                    @endforeach
                </section>
                <section style="border: medium none; padding-left: 0px; padding-right: 26px;">
                    <label for="priority">Priority</label>
                    {{ $inquiries->priority }}
                </section>
                <section style="border:none;">
                    <label for="priority">Location</label>
                    {{ $inquiries->location }}
                </section>
                @if(count($seller_inquiry) > 0)
                    <section style="border:none;">
                        <label for="delievery_date">Delievery Date</label>
                        {{ $seller_inquiry[0]->delievery_date }}
                    </section>
                @endif
            </div>
            <div style="clear:both;height:50px;"></div>
            <table class="table table-striped table-hover table-responsive datatable">
                <thead>
                    <tr>
                        <th>Item Number</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Remarks</th>
                        <?php $prices = array();?>
                        <?php $inquiry_details = json_decode($inquiries->inquir_details,true); ?>
                        @for ($i = 0; $i < count($inquiry_details); $i++)
                            <?php $j = 0; ?>
                            @foreach($seller_inquiry as $details)
                                <?php $seller_inquiry_details = json_decode($details->inquiry_details,true); ?>
                                
                                @if($i == 0)
                                    <th style="text-align:center;">{{ $details->name }}</th>
                                @endif
                                <?php $prices[$i][$j] =  $seller_inquiry_details[$i]['price']; ?>
                                <?php $j++; ?>
                            @endforeach
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach($inquiry_details as $inquiry_detail)
                        <tr id="supplier_id">
                            @foreach($inquiry_detail as $value)
                                @if (isset($value['partnum']))
                                    <td> {{ $value['partnum'] }} </td>
                                @elseif(isset($value['qty']))
                                    <td> {{ $value['qty'] }}
                                @elseif(isset($value['unit']))   
                                    {{ $value['unit'] }} 
                                    </td>
                                @elseif(isset($value['type']))   
                                    <td> {{ $value['type'] }} </td>
                                @elseif(isset($value['category']))   
                                    <td> {{ $value['category'] }} </td>
                                @elseif(isset($value['detail']))
                                    <td> {{ $value['detail'] }} </td>
                                @endif
                            @endforeach
                            @foreach($seller_inquiry as $details)
                                <?php $seller_inquiry_details = json_decode($details->inquiry_details,true);
                                $min_price = min($prices[$i]);
                                $style = '';
                                if(isset($seller_inquiry_details[$i])){
                                    $price = $seller_inquiry_details[$i]['price'];
                                    if($min_price == $price && $min_price > 0){
                                        $style =  'background: #493382 none repeat scroll 0 0;color: #fff;';
                                    }else{
                                        $style = '';
                                    }
                                }else{
                                    $price = '';
                                    $style = '';
                                }
                                if($price > 0){
                                    $price = number_format($price, 2, '.', '');
                                }else{
                                    $price = 0;
                                }
                                if($seller_inquiry_details[$i]['stock']){
                                    $stock = $seller_inquiry_details[$i]['stock'];
                                }else{
                                    $stock = 0;
                                }
                                ?>
                            <td style="{{ $style }}">
                                <ul style="list-style:none;margin:0px;text-align:center;">
                                    <li>{{ $stock."/".$price }}</li>
                                    <li>{{ $seller_inquiry_details[$i]['brand'] }}</li>
                                </ul>
                            </td>
                            @endforeach
                        </tr>
                        <?php $i = $i+1; ?>
                    @endforeach
                </tbody>
            </table>     
            <div style="clear:both;height:20px;"></div>
        </div>
    </div>
@endsection