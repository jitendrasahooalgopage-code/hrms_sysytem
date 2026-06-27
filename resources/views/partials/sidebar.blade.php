<aside id="sidebar" class="sidebar js-sidebar">
  <div class="sidebar-content js-simplebar">
    <a class="sidebar-brand" href="index.html">
      <span class="align-middle">ALGOPAGE HRMS</span>
    </a>

      <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('super.dashboard') : 
        ( Auth::user()->role->slug === 'administrator' ? route('admin.dashboard')  : 
        ( Auth::user()->role->slug === 'moderator' ? route('moderator.dashboard')  : 
         ( Auth::user()->role->slug === 'employee' ? route('employee.dashboard')  : 
         
        ( Auth::user()->role->slug === 'hr' ? route('hr.dashboard')  : route('payroll.dashboard'))))) }}">
          <i class="align-middle" data-feather="sliders"></i>
          <span class="align-middle">{{ __('Dashboard') }}</span>
        </a>
      </li>
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator'))
        <li class="sidebar-header">{{ __('Users Management') }}</li>
      {{-- @endif
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('user.index') : route('admin.users.index') }}">
          <i class="fas fa-user align-middle"></i>
          <span class="align-middle">{{ __('Manage Users') }}</span>
        </a>
        </li>
      @endif
      
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin'))
       <li class="sidebar-item">
          <a class="sidebar-link" href="{{ route('roles.index') }}">
            <i class="fas fa-user-shield align-middle"></i> <span class="align-middle">{{ __('User Settings') }}</span>
          </a>
        </li> 
      @endif
        
     @if (Auth::check() && (
    Auth::user()->role->slug === 'super-admin' ||
    Auth::user()->role->slug === 'administrator' ||
    Auth::user()->role->slug === 'hr-manager'
))

    {{-- Employee Management --}}
    <li class="sidebar-header">{{ __('Employee Management') }}</li>

     <li class="sidebar-item">
        <a class="sidebar-link"
           href="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('employee.index')
                    : (Auth::user()->role->slug === 'administrator'
                        ? route('admin.employee.index')
                        : route('hr.employee.index')) }}">
            <i class="fa-solid fa-users-viewfinder"></i>
            <span class="align-middle">{{ __('Manage Employees') }}</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('employee-assets.index') }}">
            <i class="fa-solid fa-laptop"></i>
            <span class="align-middle">{{ __('Asset Management') }}</span>
        </a>
    </li>

     <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('user-notifications.index') }}">
            <i class="fa-solid fa-laptop"></i>
            <span class="align-middle">{{ __('User Notification Management') }}</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('positions.index') }}">
            <i class="fa-solid fa-laptop"></i>
            <span class="align-middle">{{ __('Interview Management') }}</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('applications.index') }}">
            <i class="fa-solid fa-laptop"></i>
            <span class="align-middle">{{ __('Application Management') }}</span>
        </a>
    </li>

   <li class="sidebar-header">
    {{ __('Inventory Management') }}
</li>

<li class="sidebar-item">

    <a class="sidebar-link"
       href="{{ route('inventory.index') }}">

        <i class="fa-solid fa-boxes-stacked"></i>

        <span class="align-middle">

            Inventory

        </span>

    </a>

</li>

 <li class="sidebar-header">
    {{ __('Settings') }}
</li>

<li class="sidebar-item">

    <a class="sidebar-link"
       href="{{ route('holiday.index') }}">

        <i class="fa-solid fa-boxes-stacked"></i>

        <span class="align-middle">

            Holiday

        </span>

    </a>

</li>

<li class="sidebar-item">

    <a class="sidebar-link"
       href="{{ route('salary-slip.index') }}">

        <i class="fa-solid fa-boxes-stacked"></i>

        <span class="align-middle">

            Salary-slip

        </span>

    </a>

</li>

<li class="sidebar-item">

    <a class="sidebar-link"
       href="{{ route('leave-allocation.index') }}">

        <i class="fa-solid fa-boxes-stacked"></i>

        <span class="align-middle">

            Leave Allcoation

        </span>

    </a>

</li>

<li class="sidebar-item">

    <a class="sidebar-link"
       href="{{ route('employee-leave.assignments.index') }}">

        <i class="fa-solid fa-boxes-stacked"></i>

        <span class="align-middle">

            Assign Leave Allcoation

        </span>

    </a>

</li>


  
</li>

   

    {{-- Settings --}}
<li class="sidebar-header">{{ __('Organization Management') }}</li>
    <li class="sidebar-item">
        <a class="sidebar-link"
           href="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('department.index')
                    : (Auth::user()->role->slug === 'administrator'
                        ? route('admin.department.index')
                        : route('hr.department.index')) }}">
            <i class="fa-solid fa-building"></i>
            <span class="align-middle">{{ __('Manage Departments') }}</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link"
           href="{{ Auth::user()->role->slug === 'super-admin'
                    ? route('designation.index')
                    : (Auth::user()->role->slug === 'administrator'
                        ? route('admin.designation.index')
                        : route('hr.designation.index')) }}">
            <i class="fa-solid fa-user-tie"></i>
            <span class="align-middle">{{ __('Manage Designations') }}</span>
        </a>
    </li>

@endif

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'moderator'))
        <li class="sidebar-header">{{ __('Attendance Management') }}</li>
      {{-- @endif


      

      

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'moderator')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('schedule.index') : (Auth::user()->role->slug === 'administrator' ? route('admin.schedule.index') : route('moderator.schedule.index') ) }}">
          <i class="fa-solid fa-clock"></i>
          <span class="align-middle">{{ __('Schedule') }}</span>
        </a>
        </li>

        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('attendance-list.index') : (Auth::user()->role->slug === 'administrator' ? route('admin.attendance-list.index') : route('moderator.attendance-list.index') ) }}">
          <i class="fa-solid fa-clock"></i>
          <span class="align-middle">{{ __('Check Daily Attendance') }}</span>
        </a>
        </li>

        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('notifications.index') : (Auth::user()->role->slug === 'administrator' ? route('admin.notifications.index') : route('moderator.notifications.index') ) }}">
          <i class="fa-solid fa-clock"></i>
          <span class="align-middle">{{ __('Notification Settings') }}</span>
        </a>
        </li>
      {{-- @endif
      
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'moderator')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('attendance.index') : 
        (Auth::user()->role->slug === 'administrator' ? route('admin.attendance.index') : route('moderator.attendance.index') ) }}">
          <i class="fa-solid fa-calendar-days"></i>
          <span class="align-middle">{{ __('Daily Attendance') }}</span>
        </a>
        </li>
      {{-- @endif
      
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'moderator')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('sheet.report') : (Auth::user()->role->slug === 'administrator' ? route('admin.sheet.report') : route('moderator.sheet.report') ) }}">
          <i class="fa-solid fa-book"></i>
          <span class="align-middle">{{ __('Sheet Report') }}</span>
        </a>
        </li>
      {{-- @endif

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'moderator')) --}}
        {{-- <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('late.time') }}">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span class="align-middle">{{ __('Late Time') }}</span>
        </a>
        </li> --}}
      {{-- @endif
      
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'moderator')) --}}
        {{-- <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('over.time') }}">
          <i class="fa-solid fa-stopwatch"></i>
          <span class="align-middle">{{ __('Over Time') }}</span>
        </a>
        </li> --}}
      @endif
      
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' ||
       Auth::user()->role->slug === 'hr-manager'
      || Auth::user()->role->slug === 'employee'))
        <li class="sidebar-header">{{ __('Leave Management') }}</li>  

        
      {{-- @endif

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'hr-manager')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{  Auth::user()->role->slug === 'super-admin' 
         ? route('leaves.index') 
         : (
            Auth::user()->role->slug === 'administrator' 
               ? route('admin.leaves.index') 
               : (
                  Auth::user()->role->slug === 'employee'
                     ? route('employee.leaves.index')
                     : route('hr.leaves.index')
               )
           )  }}">
          <i class="fa-solid fa-person-walking-arrow-right"></i>
          <span class="align-middle">{{ __('Manage Leaves') }}</span>
        </a>
        </li>
      {{-- @endif

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'hr-manager')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('leaves.create') :
         (Auth::user()->role->slug === 'administrator' ? route('admin.leaves.create') :
         (Auth::user()->role->slug === 'employee' ? route('employee.leaves.create') : route('hr.leaves.create'))  ) }}">
          <i class="fa-solid fa-file-pen"></i>
          <span class="align-middle">{{ __('Apply Leave') }}</span>
        </a>
        </li>
      @endif

      @if(Auth::check() && Auth::user()->role->slug === 'employee')

<li class="sidebar-item">
    <a class="sidebar-link"
       href="{{ route('employee.assets') }}">

        <i class="fa-solid fa-laptop"></i>

        <span class="align-middle">
            My Assets
        </span>

    </a>
</li>

<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('employee.holidays') }}">
        <i class="fa-solid fa-calendar-day"></i>
        <span class="align-middle">My Holidays</span>
    </a>
</li>

<li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('salary-slip.my-slips') }}">
        <i class="fa-solid fa-file-invoice-dollar"></i>
        <span class="align-middle">My Payslips</span>
    </a>
</li>

@endif
          
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'payroll-manager'))
        <li class="sidebar-header">{{ __('Payroll System') }}</li>
      {{-- @endif
      
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'payroll-manager')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ Auth::user()->role->slug === 'super-admin' ? route('payroll.index') : (Auth::user()->role->slug === 'administrator' ? route('admin.payroll.index') : route('manager.payroll.index') ) }}">
          <i class="fa-solid fa-file"></i>
          <span class="align-middle">{{ __('Manage Payroll') }}</span>
        </a>
        </li>
      {{-- @endif

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'payroll-manager')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('payroll.create') }}">
          <i class="fa-solid fa-file-export"></i>
          <span class="align-middle">{{ __('Generate Payroll') }}</span>
        </a>
        </li>
      {{-- @endif

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'payroll-manager')) --}}
        <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('payroll.report') }}">
          <i class="fa-solid fa-file-export"></i>
          <span class="align-middle">{{ __('Payroll Sheet') }}</span>
        </a>
        </li>
      {{-- @endif
      
      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'payroll-manager')) --}}
        {{-- <li class="sidebar-item">
        <a class="sidebar-link" href="javascript:void(0)">
          
          <i class="fa-solid fa-wallet"></i>
          <span class="align-middle">{{ __('Gross Salary') }}</span>
        </a>
        </li> --}}
      {{-- @endif

      @if (Auth::check() && (Auth::user()->role->slug === 'super-admin' || Auth::user()->role->slug === 'administrator' || Auth::user()->role->slug === 'payroll-manager')) --}}
        {{-- <li class="sidebar-item">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fa-solid fa-clipboard"></i>
          <span class="align-middle">{{ __('Deductions') }}</span>
        </a>
        </li> --}}
      @endif
      
      
      {{-- <li class="sidebar-item">
        <a class="sidebar-link" href="javascript:void(0)">
          <i class="fa-solid fa-file-export"></i>
          <span class="align-middle">{{ __('Generate Payroll') }}</span>
        </a>
      </li> --}}
      
      
    </ul>

    
  </div>
</aside>