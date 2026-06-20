@extends('layouts.admin')


@section('title')
    Notifications
@endsection




@section('header')
    <div class="d-flex align-items-center justify-content-between mb-4">


        <h1 class="h3 mb-3">

            Notification Management

        </h1>



        <a href="{{ route('notifications.create') }}" class="btn btn-primary">

            <i class="fas fa-plus"></i>

            Create Notification

        </a>


    </div>
@endsection





@section('content')
    <section class="row">


        <div class="col-12">



            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">

                    {{ session('success') }}


                    <button type="button" class="btn-close" data-bs-dismiss="alert">

                    </button>


                </div>
            @endif





            <div class="card flex-fill">


                <div class="card-body">



                    <div class="table-responsive">



                        <table class="table table-hover data-table">


                            <thead>


                                <tr>


                                    <th>
                                        SL
                                    </th>


                                    <th>
                                        Title
                                    </th>



                                    <th>
                                        Message
                                    </th>



                                    <th>
                                        Type
                                    </th>



                                    <th>
                                        User
                                    </th>



                                    <th>
                                        Status
                                    </th>



                                    <th>
                                        Date
                                    </th>



                                    <th>
                                        Action
                                    </th>



                                </tr>
                               


                            </thead>





                            <tbody>



                                @forelse($notifications as $notification)
                                    <tr>


                                        <td>

                                            {{ $loop->iteration }}

                                        </td>





                                        <td>


                                            {{ $notification->data['title'] ?? '' }}


                                        </td>





                                        <td>


                                            {{ Str::limit($notification->data['message'] ?? '', 50) }}


                                        </td>





                                        <td>



                                            @if (($notification->data['type'] ?? '') == 'success')
                                                <span class="badge bg-success">

                                                    Success

                                                </span>
                                            @elseif(($notification->data['type'] ?? '') == 'warning')
                                                <span class="badge bg-warning text-dark">

                                                    Warning

                                                </span>
                                            @elseif(($notification->data['type'] ?? '') == 'danger')
                                                <span class="badge bg-danger">

                                                    Danger

                                                </span>
                                            @else
                                                <span class="badge bg-info">

                                                    Info

                                                </span>
                                            @endif



                                        </td>







                                        <td>


                                            @if ($notification->notifiable)
                                                {{ $notification->notifiable->name ?? 'User' }}
                                            @else
                                                N/A
                                            @endif



                                        </td>







                                        <td>


                                            @if ($notification->read_at)
                                                <span class="badge bg-secondary">

                                                    Read

                                                </span>
                                            @else
                                                <span class="badge bg-primary">

                                                    Unread

                                                </span>
                                            @endif



                                        </td>








                                        <td>


                                            {{ $notification->created_at->format('d M Y h:i A') }}


                                        </td>







                                        <td>



                                            <a href="{{ url('/notifications/read/' . $notification->id) }}"
                                                class="btn btn-sm btn-outline-success">


                                                <i class="fas fa-eye"></i>


                                            </a>






                                            <form action="{{ url('/notifications/delete/' . $notification->id) }}"
                                                method="POST" class="d-inline">


                                                @csrf

                                                @method('DELETE')


                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete notification?')">


                                                    <i class="fas fa-trash"></i>


                                                </button>


                                            </form>




                                        </td>




                                    </tr>





                                @empty



                                    <tr>


                                        <td colspan="8" class="text-center text-muted">


                                            No notifications found


                                        </td>


                                    </tr>
                                @endforelse





                            </tbody>



                        </table>



                    </div>


                </div>


            </div>



        </div>


    </section>
@endsection
