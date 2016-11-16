<h2>Accounts</h2>
<div id="accordion">
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
  <h3>Preffered Suppliers</h3>
  <div>
    List of top 5 Preffered Suplpie
    Link to Management of Preddered Supplier
  </div>
</div>