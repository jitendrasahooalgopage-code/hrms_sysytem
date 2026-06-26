<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <ul class="navbar-nav navbar-align">
        <li class="nav-item dropdown">
            <a class="nav-icon nav-link dropdown-toggle" href="javascript:void(0)" id="itemsDropdown" data-bs-toggle="dropdown">
                <i class="align-middle" data-feather="plus"></i>
                <span class="align-middle" style="font-size: 0.85rem;">New Items</span>
            </a>
            <div class="dropdown-menu py-0" aria-labelledby="itemsDropdown">
                <div class="dropdown-menu-header">{{ __('Add New Option') }}</div>
                <div class="list-group">
                    <a href="{{ route('department.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Department') }}</span>
                    </a>
                    <a href="{{ route('designation.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Designation') }}</span>
                    </a>
                    <a href="{{ route('employee.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Employee') }}</span>
                    </a>
                    <a href="{{ route('attendance.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Attendance') }}</span>
                    </a>
                    <a href="{{ route('leaves.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Leave') }}</span>
                    </a>
                    <a href="{{ route('payroll.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('Payroll') }}</span>
                    </a>
                    <a href="{{ route('user.create') }}" class="list-group-item">
                        <i class="fas fa-plus align-middle"></i>
                        <span class="text-dark ps-2">{{ __('User') }}</span>
                    </a>
                </div>
            </div>
        </li>
    </ul>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align">

            {{-- ───── Check-In Action Trigger ───── --}}
            <li class="nav-item" id="navCheckinItem">
                <a class="nav-link" href="javascript:void(0)" id="navCheckinBtn" title="Check In">
                    <i class="align-middle text-success" data-feather="log-in"></i>
                    <span class="align-middle d-none d-sm-inline ms-1 text-success" style="font-size: 0.85rem; font-weight: 600;">Check In</span>
                </a>
            </li>

            {{-- ───── Check-Out Action Trigger ───── --}}
            <li class="nav-item me-2" id="navCheckoutItem">
                <a class="nav-link disabled opacity-50" href="javascript:void(0)" id="navCheckoutBtn" title="Check Out — check in first" style="pointer-events: none; cursor: not-allowed;">
                    <i class="align-middle text-warning" data-feather="log-out"></i>
                    <span class="align-middle d-none d-sm-inline ms-1 text-warning" style="font-size: 0.85rem; font-weight: 600;">Check Out</span>
                </a>
            </li>

            {{-- ───── Notifications Dropdown ───── --}}
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
                    <div class="position-relative">
                        <i class="align-middle" data-feather="bell"></i>
                        @if ($unreadNotifications > 0)
                            <span class="indicator">{{ $unreadNotifications }}</span>
                        @endif
                    </div>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                    <div class="dropdown-menu-header">
                        @if ($unreadNotifications)
                            {{ $unreadNotifications }} New Notifications
                        @else
                            No New Notifications
                        @endif
                    </div>

                    <div class="list-group">
                        @forelse($notifications as $notification)
                            <a href="{{ route('notification.read', $notification->id) }}" class="list-group-item">
                                <div class="row g-0 align-items-center">
                                    <div class="col-2">
                                        @if (($notification->data['type'] ?? '') == 'danger')
                                            <i class="text-danger" data-feather="alert-circle"></i>
                                        @elseif(($notification->data['type'] ?? '') == 'warning')
                                            <i class="text-warning" data-feather="bell"></i>
                                        @else
                                            <i class="text-success" data-feather="check-circle"></i>
                                        @endif
                                    </div>

                                    <div class="col-10">
                                        <div class="text-dark">
                                            {{ $notification->data['title'] }}
                                            @if (!$notification->read_at)
                                                <span class="badge bg-primary">New</span>
                                            @endif
                                        </div>
                                        <div class="text-muted small mt-1">
                                            {{ $notification->data['message'] }}
                                        </div>
                                        <div class="text-muted small mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center p-3 text-muted">
                                No notifications
                            </div>
                        @endforelse
                    </div>

                    <div class="dropdown-menu-footer">
                        <a href="{{ route('notifications.index') }}" class="text-muted">Show all notifications</a>
                    </div>
                </div>
            </li>

            {{-- ───── Messages Dropdown ───── --}}
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle" href="#" id="messagesDropdown" data-bs-toggle="dropdown">
                    <div class="position-relative">
                        <i class="align-middle" data-feather="message-square"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="messagesDropdown">
                    <div class="dropdown-menu-header">
                        <div class="position-relative">4 New Messages</div>
                    </div>
                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-5.jpg') }}" class="avatar img-fluid rounded-circle" alt="Vanessa Tucker" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">Vanessa Tucker</div>
                                    <div class="text-muted small mt-1">Nam pretium turpis et arcu. Duis arcu tortor.</div>
                                    <div class="text-muted small mt-1">15m ago</div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-2.jpg') }}" class="avatar img-fluid rounded-circle" alt="William Harris" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">William Harris</div>
                                    <div class="text-muted small mt-1">Curabitur ligula sapien euismod vitae.</div>
                                    <div class="text-muted small mt-1">2h ago</div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-4.jpg') }}" class="avatar img-fluid rounded-circle" alt="Christina Mason" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">Christina Mason</div>
                                    <div class="text-muted small mt-1">Pellentesque auctor neque nec urna.</div>
                                    <div class="text-muted small mt-1">4h ago</div>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="list-group-item">
                            <div class="row g-0 align-items-center">
                                <div class="col-2"><img src="{{ asset('img/avatars/avatar-3.jpg') }}" class="avatar img-fluid rounded-circle" alt="Sharon Lessman" /></div>
                                <div class="col-10 ps-2">
                                    <div class="text-dark">Sharon Lessman</div>
                                    <div class="text-muted small mt-1">Aenean tellus metus, bibendum sed, posuere ac, mattis non.</div>
                                    <div class="text-muted small mt-1">5h ago</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="dropdown-menu-footer">
                        <a href="#" class="text-muted">Show all messages</a>
                    </div>
                </div>
            </li>

            {{-- ───── User Dropdown ───── --}}
            <li class="nav-item dropdown">
                <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                    <i class="align-middle" data-feather="settings"></i>
                </a>
                <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                    <img src="{{ asset('img/avatars/dummy.png') }}" class="avatar img-fluid rounded me-1" alt="{{ Auth::user()->name }}" />
                    <span class="text-dark">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>
                    <a class="dropdown-item" href="javascript:void(0)"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="align-middle me-1" data-feather="log-out"></i>
                            <span class="me-1">{{ __('Log Out') }}</span>
                        </a>
                    </form>
                </div>
            </li>

        </ul>
    </div>
</nav>