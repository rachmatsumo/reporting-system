<h2>{{ $report->reportDesign->name }}</h2>
<p>Status : {{ ucfirst($report->status) }}</p>
<p>Dibuat oleh : {{ $report->creator->name }}</p>
<p>Tanggal : {{ $report->created_at->format('d/m/Y H:i') }}</p>

<hr>

<h4>Isi Report:</h4>
<pre>{{ print_r($report->data, true) }}</pre>
