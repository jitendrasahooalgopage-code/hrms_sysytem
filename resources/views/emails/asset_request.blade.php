<h2>📢 New Asset Request Submitted</h2>

<hr>

<h3>Employee Information</h3>

<p>
<b>Name:</b>
{{ $assetRequest->employee->firstname ?? '' }}
{{ $assetRequest->employee->lastname ?? '' }}
</p>

<p>
<b>Email:</b>
{{ $assetRequest->employee->email ?? '' }}
</p>

<p>
<b>Employee ID:</b>
{{ $assetRequest->employee->unique_id ?? '' }}
</p>

<p>
<b>Phone:</b>
{{ $assetRequest->employee->phone ?? '' }}
</p>

<hr>

<h3>Request Information</h3>

<p>
<b>Request Type:</b>
{{ $assetRequest->request_type }}
</p>

<p>
<b>Subject:</b>
{{ $assetRequest->subject }}
</p>

<p>
<b>Message:</b>
{{ $assetRequest->message }}
</p>

<p>
<b>Status:</b>
{{ $assetRequest->status }}
</p>

<p>
<b>Created At:</b>
{{ $assetRequest->created_at->format('d M Y h:i A') }}
</p>

<hr>

<h3>Asset Information</h3>

<p>
<b>Asset:</b>
{{ $assetRequest->asset->asset_name ?? 'N/A' }}
</p>