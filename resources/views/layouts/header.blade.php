
<?php 
$system_name = App\Setting::where('name', 'System name')->first()->value;
?>

<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    {{-- <a class="navbar-brand navbar-brand-full logo-text" href="{{ url('/') }}">SBurK</a>
    <a class="navbar-brand navbar-brand-minimized logo-text" href="{{ url('/') }}">SBK</a> --}}
    <a class="navbar-brand" href="{{ url('/') }}">
        <span class="navbar-brand-full logo-text">{{$system_name}}</span>
        <span class="navbar-brand-minimized logo-text">{{$system_name}}</span>
        {{-- <img class="navbar-brand-full" src="/svg/modulr.svg" width="89" height="25" alt="Modulr Logo">
        <img class="navbar-brand-minimized" src="/svg/modulr-icon.svg" width="30" height="30" alt="Modulr Logo"> --}}
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <?php 
        $school = Auth::user();
        $authSetting = App\AuthSetting::first();
        if($authSetting == null || $authSetting->secure_key == null
        || $authSetting->u1 == null
        || $authSetting->u2 == null
        || $authSetting->u3 == null)
        {?>
            <div class="alert alert-danger ml-auto" role="alert">
                Please <strong><a href="/activation">activate</a></strong> your copy.
            </div>
        <?php
        }
        else
        {
            $google_maps_setting = App\Setting::where('name','Google maps API key')->first()->value;
            $mapbox_setting = App\Setting::where('name','Mapbox default public token')->first()->value;
            if ((!$school->is_super_admin_account))
            {
                if((!$school->address || !$school->latitude || !$school->longitude))
                    {?>
                        <div class="alert alert-danger ml-auto" role="alert">
                            Please update your school address
                        </div>
                <?php
                    }
            }
            else
            {
                if($google_maps_setting==null && $mapbox_setting==null)
                { ?>
                <div class="alert alert-danger ml-auto" role="alert">
                    Please update your settings
                </div>
            <?php }
            }
        }
        ?>
        


    <ul class="nav navbar-nav ml-auto mr-3">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <img class="img-avatar" src="{{Auth::user()->avatar_url}}">
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow mt-2">
                <a class="dropdown-item">
                    {{ Auth::user()->name }}<br>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </a>
                <a class="dropdown-item" href="/profile">
                    <i class="fas fa-user"></i> Profile
                </a>
                <?php $school = Auth::user();
                if (!$school->is_super_admin_account) { ?>
                        <?php 
                        if($school->plan->is_pay_as_you_go != 1 && $school->plan->allowed_children > 0)
                        {
                            $current_children = 0;
                            for ($x = 0; $x < $school->parents->count(); $x++) {
                                for ($y = 0; $y < $school->parents[$x]->children->count(); $y++) {
                                    $current_children++;
                                }
                            }    
                            ?>
                            <a class="dropdown-item" href="/plan">
                                <div class="mb-2">
                                    <i class="fas fa-trophy"></i> Plan <span class="badge badge-primary">{{ Auth::user()->plan->name }}</span>
                                </div>
                                <?php if($current_children < $school->plan->allowed_children) { ?>
                                    <i class="fas fa-chair"></i> Seats <span class="badge badge-primary">
                                        {{ $current_children }} / {{ $school->plan->allowed_children }}</span>
                                <?php } else { ?>
                                    <i class="fas fa-chair"></i> Seats <span class="badge badge-danger">
                                        {{ $current_children }} / {{ $school->plan->allowed_children }}</span>
                                <?php }  ?>
                            </a>
                        <?php }
                        else
                        { ?>
                            <a class="dropdown-item" href="/plan">
                                <i class="fas fa-trophy"></i> Plan <span class="badge badge-primary">{{ Auth::user()->plan->name }}</span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                <div class="divider"></div>
                <a class="dropdown-item" href="/password">
                    <i class="fas fa-key"></i> Password
                </a>
                <div class="divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</header>
