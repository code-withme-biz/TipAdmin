
@if(Auth::guard('admin')->check())
    @include('layouts.navbars.navs.auth')
@else
    @include('layouts.navbars.navs.guest')
@endif
