{{-- Not in use --}}
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Report</th>
            <th>Status</th>
            <th>Dibuat Oleh</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reports as $i => $r)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r->reportDesign->name }}</td>
            <td>{{ $r->status }}</td>
            <td>{{ $r->creator->name ?? '-' }}</td>
            <td>{{ $r->created_at->format('d/m/Y') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
