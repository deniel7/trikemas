<!-- Sidebar user panel (optional) -->
        <div class="user-panel">
          <div class="pull-left image">
            <img src="http://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?d=identicon" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p>{{ Auth::user()->name }}</p>
            <!-- Status -->
            <a href="{{ url('user/profile') }}"><i class="fa fa-circle text-success"></i> {{ Auth::user()->rolename }}</a>
          </div>
        </div>
          