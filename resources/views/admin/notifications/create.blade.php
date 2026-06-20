@extends('layouts.admin')


@section('content')
    <div class="card">

        <div class="card-header">

            <h4>
                Create Notification
            </h4>

        </div>



        <div class="card-body">


            @if (session('success'))
                <div class="alert alert-success">

                    {{ session('success') }}

                </div>
            @endif




            <form method="POST" action="{{ route('notifications.send') }}">

                @csrf



                <label>
                    Send To
                </label>


                <select name="target" id="target" class="form-control">


                    <option value="user">
                        Individual User
                    </option>


                    <option value="all">
                        All Users Announcement
                    </option>


                    <option value="role">
                        Group / Role
                    </option>


                </select>




                <br>



                <div id="userBox">


                    <label>
                        Select User
                    </label>


                    <select name="user_id" class="form-control">


                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">

                                {{ $user->name }}

                            </option>
                        @endforeach


                    </select>


                </div>





                <div id="roleBox" style="display:none">


                    <label>
                        Role ID
                    </label>


                    <input type="number" name="role_id" class="form-control">


                </div>




                <br>


                <label>
                    Title
                </label>

                <input class="form-control" name="title">



                <br>


                <label>
                    Message
                </label>


                <textarea class="form-control" name="message"></textarea>




                <br>


                <label>
                    Type
                </label>


                <select name="type" class="form-control">


                    <option value="info">
                        Info
                    </option>

                    <option value="success">
                        Success
                    </option>

                    <option value="warning">
                        Warning
                    </option>

                    <option value="danger">
                        Danger
                    </option>


                </select>



                <br>



                <button class="btn btn-primary">

                    Send Notification

                </button>


            </form>



        </div>


    </div>



    <script>
        document
            .getElementById('target')
            .onchange = function() {


                let val = this.value;


                document.getElementById('userBox')
                    .style.display =
                    val == "user" ? "block" : "none";



                document.getElementById('roleBox')
                    .style.display =
                    val == "role" ? "block" : "none";



            }
    </script>
@endsection
