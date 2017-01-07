@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-sm-10 col-sm-offset-2">
            <h2>Inquiry Form</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        {!! implode('', $errors->all('
                        <li class="error">:message</li>
                        ')) !!}
                    </ul>
                </div>
            @endif
        </div>
    </div>
    
    {!! Form::open(['route' => 'inquiry.store', 'class' => 'form-horizontal','id' => 'frmInquiry']) !!}
        {{ csrf_field() }}
        <div id="inquiry-wizard">
            <h3>Step 1 - Inquiry</h3>
            <section>
                <div ng-controller="appCtrl">
                    <p><% test.text %>, world </p>
                </div>
                
                <div id="inquiry_details">                <?php for($i=1; $i<6;$i++){ ?>
                    <div class="row uniform inquiry-fields" id="rows{{ $i }}">
                        <div class="2u 12u$(xsmall)">
                            <input type="text" name="inqpost[][partnum]" id="inqpost[][partnum]" value="" placeholder="Part Number" />
                        </div>
                        <div class="1u 6u$(xsmall)">
                            <input type="text" name="inqpost[][qty]" id="inqpost[][qty]" value="" placeholder="Qty" />
                        </div>
                        <div class="2u 12u$(xsmall)">
                            <div class="select-wrapper">
                                <select name="inqpost[][unit]" id="inqpost[][unit]">
                                    <option value="Pcs">Pcs</option>
                                    <option value="Ctn">Ctn</option>
                                    <option value="Box">Box</option>
                                    <option value="KG">KG</option>
                                    <option value="Ltr">Ltr</option>
                                    <option value="ml">ml</option>
                                </select>
                            </div>
                        </div>
                        <div class="2u 12u$(xsmall)">
                            <div class="select-wrapper">
                                <select name="inqpost[][type]" id="inqpost[][type]">
                                    <option value="Genuine">Genuine</option>
                                    <option value="Aftermarket">Aftermarket</option>
                                    <option value="Any">Any</option>

                                </select>
                            </div>
                        </div>	
                        <div class="2u 12u$(xsmall)">
                            <div class="select-wrapper">
                                <select name="inqpost[][category]" id="inqpost[][category]">
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="3u 12u$(xsmall)">
                            <input type="text" name="inqpost[][detail]" id="inqpost[][detail]" value="" placeholder="More Details..." />
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="row uniform">
                    <div class="12u$">
                        <ul class="actions">
                            <li><a href="#" class="button scrolly" id="btnadd">Add more + 10</a></li>	
                            <li id="btnremove-li" style="display:none;float: left; margin-right: 10px;"><a href="#" class="button scrolly" id="btnremove">Remove Last</a></li>
                        </ul>
                    </div>
                </div>
            </section>
            <h3>Step 2 - Suppliers</h3>
            <section>
                <div class="row uniform">
                @if($selected_customers->count() > 0)
                    <div class="6u 12u$(xsmall)">
                        <h4>Frequent Supplier</h4>
                        <table class="table table-striped table-hover table-responsive" style="margin-top: 25px;">
                            <thead>
                                <tr>
                                    <th>
                                        <label><input type="checkbox" id="checkbox_preffered_supplier"/> <b>Check all</b> </label>
                                    </th>
                                    <th>Company Name</th>
                                    <th>Deals In</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selected_customers as $selected_customer)
                                    <tr>
                                        <td>
                                            <label>
                                                <input value="{{ $selected_customer->id }}" class="checkbox" id="preffered_supplier" name="inquiry-supplier[]" type="checkbox">
                                                <span>{{ $selected_customer->name }}</span>
                                            </label>
                                        </td>
                                        <td>{{ $selected_customer->company_name }}</td>
                                        <td>{{ $selected_customer->mobile_phone }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div ng-controller="InquiryController" class="6u 12u$(xsmall)">
                        <div class="split style2">
                            <section style="border: none;padding:0;">
                                <h4>Other/ ALL  Supplier</h4>
                            </section>
                            <section style="border: none;padding:0;margin-top: -10px;margin-bottom: 15px;">
                                <form class="form-inline">
                                   <input ng-model="query" type="text"
                                     placeholder="Search Supplier" autofocus>
                                 </form>
                            </section>
                        </div>  
                    <table id="supplier-list" class="table table-striped table-hover table-responsive">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="checkbox_supplier"/> 
                                    <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Name
                                    </a>
                                </th>
                                <th><a href="#" ng-click="sortType = 'company_name'; sortReverse = !sortReverse">Company Name</a></th>
                                <th><a href="#" ng-click="sortType = 'mobile_phone'; sortReverse = !sortReverse">Deals In</a></th>
                            </tr>
                        </thead>
                @else 
                   <div ng-controller="InquiryController" class="12u$ 12u$(xsmall)"> 
                        <div class="split style2">
                            <section style="border: none;padding:0;">
                                <h4>Other/ ALL  Supplier</h4>
                            </section>
                            <section style="border: none;padding:0;margin-top: -10px;margin-bottom: 15px;">
                                <form class="form-inline">
                                   <input ng-model="query" type="text"
                                     placeholder="Search Supplier" autofocus>
                                 </form>
                            </section>
                        </div>
                    <table id="supplier-list" id="supplier-list" class="table table-striped table-hover table-responsive">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="checkbox_supplier"/> 
                                    <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                        Name
                                    </a>
                                </th>
                                <th><a href="#" ng-click="sortType = 'company_name'; sortReverse = !sortReverse">Company Name</a></th>
                                <th><a href="#" ng-click="sortType = 'mobile_phone'; sortReverse = !sortReverse">Deals In</a></th>
                            </tr>
                        </thead>
                @endif
                        <tbody>
                            <tr ng-repeat="supplier in suppliers | orderBy:sortType:sortReverse | limitTo: paginationLimit() | filter:query">
                              <td> 
                                  <label>
                                    <input value="<% supplier.id %>" class="checkbox" id="supplier" name="inquiry-supplier[]" type="checkbox">
                                    <span><% supplier.name %></span>
                                    </label>
                              </td>
                              <td><% supplier.company_name %></td>
                              <td><% supplier.mobile_phone %></td>
                            </tr>
                          </tbody>
                            <tfoot>
                                <td colspan="3">
                                    <button type="button" ng-show="hasMoreItemsToShow()" ng-click="showMoreItems()">
                                        Load more
                                   </button>
                                </td>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>
            <h3>Step 3 - Send Inquiry</h3>
            <section>
                <div class="row uniform">
                    <div class="6u 12u$(xsmall)">
                        <h2>Inquiry Details</h2>
                    </div>
                    <div class="6u 12u$(xsmall)">
                        <p style="margin-bottom:0;"> (For Client's reference only, This Information will not be shared with Suppliers )</p>
                        <small>Account Reference</small>
                    </div>
                    <div style="clear:both"></div>
                    <div class="6u 12u$(xsmall)">    
                        <div class="12u$ 12u$(xsmall)">
                            <label style="width:30%; float: left; margin-right: 20px;" for="priority">Set Status As</label>
                            <select style="float: left; width: 65%; margin-top: -10px;" name="priority" id="priority">
                                <option value="Urgent">Urgent</option>
                                <option value="Normal">Normal</option>
                            </select>
                        </div>
                        <div class="12u$ 12u$(xsmall)" style="padding-top: 25px;">
                            <label style="width:30%; float: left; margin-right: 20px;" for="delivery_required">Delivery Required</label>
                            <select style="float: left; width: 65%; margin-top: -10px;" name="delivery_required" id="delivery_required">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="12u$ 12u$(xsmall)" style="padding-top: 25px;">
                            <label style="width:30%; float: left; margin-right: 20px;" for="location">Location</label>
                            <input style="float: left; width: 65%; margin-top: -10px;" type="text" name="location" id="location" />
                        </div>
                    </div>
                    <div class="5u 12u$(xsmall)">
                        <div class="12u$ 12u$(xsmall)">
                            <input style="margin-top: -7px;" placeholder="Information 1" type="text" name="inqpost[location1]" />
                        </div>
                        <div class="12u$ 12u$(xsmall)">
                            <input style="margin-top: 15px;" placeholder="Information 2" type="text" name="inqpost[location2]" />
                        </div>
                        <div class="12u$ 12u$(xsmall)" style="margin-top: 35px; margin-bottom: 0px;">
                            <p>Send Inquiry will give us inquiry number</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    {!! Form::close() !!}

@endsection

