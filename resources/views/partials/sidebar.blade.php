<h2>Accounts</h2>
<div id="accordion">
  <h3>Balance</h3>
  <div>
    <h3>Current Balance</h3>
    <span>{{ Auth::user()->balance }}</span>
    @if(Auth::user()->balance < 3)
        <span>You account will be discharged soon please <a href='#contactus'>Contact Us</a> to recharge your account</span>
    @else
        <h4>Account Status</h4>
        <span>Charged</span>
    @endif
  </div>
  <h3>Account Info</h3>
  <div>
    <h3>Name</h3>
    <span>{{ Auth::user()->name }}</span>
    <h3>Company Name</h3>
    <span>{{ Auth::user()->company_name }}</span>
    <h3>Contact No.</h3>
    <span>{{ Auth::user()->mobile_phone }}</span>
    <h3>eMail</h3>
    <div>
        <a href="mailto:{{ Auth::user()->email }}">{{ Auth::user()->email }}</a>
    </div>
    <div>
        <a href="{{ url('/profile') }}" class="dropdown-toggle">
            Change Password
        </a>
    </div>
  </div>
  <h3>Preffered Suppliers</h3>
  <div>
        List of top 5 Preffered Suplpie
        @if($selected_customers->count() > 0)
            <table class="table table-striped table-hover table-responsive" style="margin-top: 25px;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Deals In</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($selected_customers as $selected_customer)
                        <tr>
                            <td>{{ $selected_customer->name }}</td>
                            <td>{{ $selected_customer->mobile_phone }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
  </div>
</div>