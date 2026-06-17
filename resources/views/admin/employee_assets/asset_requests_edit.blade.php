@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="card shadow border-0">

        <div class="card-header">

            <h4>
                Update Asset Request
            </h4>

        </div>

        <div class="card-body">

            <form
    action="{{ route('employee-assets.requests.update',$requestData->id) }}"
    method="POST">

    @csrf
    @method('PUT')

                <div class="row">

                    <div class="col-md-6">

                        <div class="card border">

                            <div class="card-header">

                                Employee Information

                            </div>

                            <div class="card-body">

                                <p>
                                    <strong>Name:</strong>

                                    {{ $requestData->employee->firstname }}
                                    {{ $requestData->employee->lastname }}
                                </p>

                                <p>
                                    <strong>Email:</strong>

                                    {{ $requestData->employee->email }}
                                </p>

                                <p>
                                    <strong>Phone:</strong>

                                    {{ $requestData->employee->phone }}
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="card border">

                            <div class="card-header">

                                Request Information

                            </div>

                            <div class="card-body">

                                <p>
                                    <strong>Type:</strong>

                                    {{ $requestData->request_type }}
                                </p>

                                <p>
                                    <strong>Subject:</strong>

                                    {{ $requestData->subject }}
                                </p>

                                <p>
                                    <strong>Message:</strong>

                                    {{ $requestData->message }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="mt-4">

                    <label class="form-label">

                        Uploaded Images

                    </label>

                    <div class="d-flex flex-wrap gap-3">

                        @foreach($requestData->photos as $photo)

                            <a href="{{ asset('storage/'.$photo) }}"
                               target="_blank">

                                <img
                                    src="{{ asset('storage/'.$photo) }}"
                                    width="120"
                                    class="rounded border">

                            </a>

                        @endforeach

                    </div>

                </div>

                <div class="mt-4">

                    <label class="form-label">

                        Admin Remark

                    </label>

                    <textarea
                        name="admin_remark"
                        class="form-control"
                        rows="4">{{ $requestData->admin_remark }}</textarea>

                </div>

                <div class="mt-3">

                    <label class="form-label">

                        Status

                    </label>

                    <select
                        name="status"
                        class="form-control">

                        <option value="Pending">
                            Pending
                        </option>

                        <option value="Approved">
                            Approved
                        </option>

                        <option value="Rejected">
                            Rejected
                        </option>

                        <option value="Completed">
                            Completed
                        </option>

                    </select>

                </div>

                <div class="mt-4">

                    <button
    type="submit"
    class="btn btn-success">

    Save Changes

</button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection