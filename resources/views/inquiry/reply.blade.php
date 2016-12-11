@extends('layouts.app')

@section('content')
    <h2>Inquiry List</h2>
    <div class="row uniform">
        <div class="12u$">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/inquiry/update/') }}">
        {{ csrf_field() }}
            <div class="split style6">
                <section style="border:none;">
                    <label for="id">Inquiry #</label>
                    <input readonly value="{{ $inquiries->id }}" type="text" name="id" id="id" />
                </section>
                <section style="border: medium none; padding-left: 0px; padding-right: 26px;">
                    <label for="created_at">Date</label>
                    <input readonly value="{{ Carbon\Carbon::parse($inquiries->created_at)->format('Y-m-d') }}" type="text" name="created_at" id="created_at" />
                </section>
                <section style="border:none;">
                    <label for="id">Customer</label>
                    @foreach($customers as $customer)
                        @if($customer->id == $inquiries->customer_id)
                       <input readonly value="{{ $customer->name }}" type="text" />
                       @endif
                    @endforeach
                    <input type ="hidden" name="customer_id" id="customer_id" value="{{ $inquiries->customer_id }}" />
                </section>
                <section style="border: medium none; padding-left: 0px; padding-right: 26px;">
                    <label for="priority">Priority</label>
                    <input readonly value="{{ $inquiries->priority }}" type="text" name="priority" id="priority" />
                </section>
                <section style="border:none;">
                    <label for="priority">Location</label>
                    <input readonly value="{{ $inquiries->location }}" type="text" name="location" id="priority" />
                </section>
                <section style="border:none;">
                    <label for="delievery_date">Delievery Date</label>
                    <input required class="datepicker" value="{{ $inquiries->delievery_date }}" type="text" name="delievery_date" id="delievery_date" />
                </section>
            </div>
            <div style="clear:both;height:50px;"></div>
            <table class="table table-striped table-hover table-responsive datatable">
                <thead>
                    <tr>
                        <th>Item Number</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Remarks</th>
                        <th>Available Stock</th>
                        <th>Price AED</th>
                        <th>Brand</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $inquiry_details = json_decode($inquiries->inquir_details,true);$i = 0; 
                    $seller_inquiry_details = json_decode($inquiries->seller_inquiry_details,true);
                    ?>
                    @foreach($inquiry_details as $inquiry_detail)                       
                        <?php if(isset($seller_inquiry_details[$i])){ $seller_inquiry_detail = $seller_inquiry_details[$i]; } ?>
                        <tr id="supplier_id">
                            @foreach($inquiry_detail as $value)
                                @if (isset($value['partnum']))
                                    <td> {{ $value['partnum'] }} </td>
                                @elseif(isset($value['qty']))
                                    <td> {{ $value['qty'] }} </td>
                                @elseif(isset($value['unit']))   
                                    <td> {{ $value['unit'] }} </td>
                                @elseif(isset($value['type']))   
                                    <td> {{ $value['type'] }} </td>
                                @elseif(isset($value['category']))   
                                    <td> {{ $value['category'] }} </td>
                                @elseif(isset($value['detail']))
                                    <td> {{ $value['detail'] }} </td>
                                @endif
                            @endforeach
                            <?php if(isset($seller_inquiry_details[$i])){ ?>
                            <td> <input type="text" name="inqpost[{{ $i }}][stock]" value="{{ $seller_inquiry_detail['stock']}}" /> </td>
                            <td> <input type="text" name="inqpost[{{ $i }}][price]" value="{{ $seller_inquiry_detail['price']}}" /> </td>
                            <td> <input type="text" name="inqpost[{{ $i }}][brand]" value="{{ $seller_inquiry_detail['brand']}}" /> </td>
                            <?php }else{
                                ?>
                            <td> <input type="text" name="inqpost[{{ $i }}][stock]" value="" /> </td>
                            <td> <input type="text" name="inqpost[{{ $i }}][price]" value="" /> </td>
                            <td> <input type="text" name="inqpost[{{ $i }}][brand]" value="" /> </td>
                            <?php 
                            }
?>
                        </tr>
                        <?php $i = $i+1; ?>
                    @endforeach
                </tbody>
            </table>            
            <div style="clear:both;height:20px;"></div>
            <div class="12u$" style="float:right">
                <ul class="actions">
                    <li>
                        <input id="reset-inquiry-details" type="button" class="button scrolly" value="Reset" />
                    </li>
                    <li>
                        <input type="submit" class="button scrolly" value="Send Reply" />
                    </li>
                </ul>
            </div>
            <input type="hidden" name="seller_id" value="{{ $inquiries->seller_id }}" />
            <div style="clear:both;height:20px;"></div>
        </form>
        </div>
    </div>
@endsection