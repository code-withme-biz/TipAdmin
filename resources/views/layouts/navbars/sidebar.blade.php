<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text logo-mini">{{ __('') }}</a>
            <a href="#" class="simple-text logo-normal">{{ __('TiP Admin Panel') }}</a>
        </div>
        <ul class="nav">
            <li class="{{ request()->is('home') ? 'active' : '' }}">
                <a href="{{ URL::to('home') }}">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>{{ __('Dashboard') }}</p>
                </a>
            </li>
            <li class="{{ request()->is('user') || request()->is('user/add') || request()->is('user/edit/*') ? 'active' : '' }}">
                <a href="{{ URL::to('user') }}">
                    <i class="tim-icons icon-single-02"></i>
                    <p>{{ __('User Management') }}</p>
                </a>
            </li>
            <li class="{{ request()->is('artist') || request()->is('artist/add') || request()->is('artist/edit/*') ? 'active' : '' }}">
                <a href="{{ URL::to('artist') }}">
                    <i class="tim-icons icon-badge"></i>
                    <p>{{ __('Artist Management') }}</p>
                </a>
            </li>

            <li class="{{ request()->is('transaction') ? 'active' : '' }}">
                <a href="{{ URL::to('transaction') }}">
                    <i class="tim-icons icon-money-coins"></i>
                    <p>{{ __('Transaction Management') }}</p>
                </a>
            </li>

            <li class="{{ request()->is('content') || request()->is('content/add') || request()->is('content/edit/*') ? 'active' : '' }}">
                <a href="{{ URL::to('content') }}">
                    <i class="tim-icons icon-notes"></i>
                    <p>{{ __('Content Management') }}</p>
                </a>
            </li>

            <!-- <li class="{{ request()->is('karma-points') || request()->is('karma-points/add') || request()->is('karma-points/edit/*') ? 'active' : '' }}">
                <a href="{{ URL::to('karma-points') }}">
                    <i class="tim-icons icon-coins"></i>
                    <p>{{ __('Karma Points') }}</p>
                </a>
            </li> -->
        </ul>
    </div>
</div>
