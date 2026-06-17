@extends('layouts.admin')

@section('content')

<div class="container">

    <div class="card">

        <div class="card-header">
            <h4>My Assets</h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Asset</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($assets as $asset)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>

                            @foreach($asset->asset_details as $item)

                                {{ $item['asset'] }}
                                ({{ $item['qty'] }})

                                <br>

                            @endforeach

                        </td>

                        <td>
                            {{ $asset->status }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="3">
                            No Assets Assigned
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection