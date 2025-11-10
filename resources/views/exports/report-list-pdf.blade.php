<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        h2 {
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
        }

        .meta {
            margin-bottom: 5px;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
        }

        .section {
            margin-top: 20px;
        }

        .field {
            margin: 5px 0;
            padding-left: 10px;
        }

        .field strong {
            width: 200px;
            display: inline-block;
            vertical-align: top;
        }

        .field img {
            max-width: 300px;
            height: auto;
            margin-top: 5px;
            border: 1px solid #ccc;
        }

        .main-title {
            margin-top: 25px;
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .sub-title {
            margin-top: 25px;
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 4px;
        }

        th {
            background: #f8f8f8;
        }

        .no-data {
            color: #888;
            font-style: italic;
            padding-left: 10px;
        }

        .map-static {
            display: block;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .border-none table, .border-none th, .border-none td {
            border: none !important;
        }

        .text-bold {
            font-weight: bold;
        }

        .font-wrap {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            overflow-wrap: break-word;
            max-width: 250px;
            display: inline-block;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

@foreach($reports as $report)
    <div class="meta">
        <table class="border-none">
            <tr>
                <td width="0">Form Report</td>
                <td><b>{{ $report->reportDesign->name }}</b></td>
            </tr>
            <tr>
                <td width="0">Status</td>
                <td><b>{{ ucfirst($report->status) }}</b></td>
            </tr>
            <tr>
                <td width="0">Dibuat oleh</td>
                <td><b>{{ $report->creator->name ?? 'Unknown' }}</b></td>
            </tr>
            <tr>
                <td width="0">Tanggal</td>
                <td><b>{{ $report->created_at->format('d/m/Y H:i') }}</b></td>
            </tr>
        </table>
    </div>

    {{-- MAIN FIELDS --}}
    <div class="section"> 
        <div class="main-title">{{ $report->reportDesign->name }}</div>

        <table class="border-none">
            @foreach($report->reportDesign->fields as $field)
                @php
                    $value = $report->data[$field->name] ?? null;
                @endphp
                <tr class="field">
                    <td class="font-wrap">{{ $field->label }}</td>
                    <td class="text-bold">@include('exports.partials.pdf-field-display', ['field' => $field, 'value' => $value])</td>
                </tr>
            @endforeach
        </table>
    </div>

    {{-- SUBDESIGNS --}}
    @foreach($report->reportDesign->subDesigns as $subDesign)
        <div class="section">
            <div class="sub-title">{{ $subDesign->name }}</div>
            @php
                $subItems = $report->subData->where('report_sub_design_id', $subDesign->id);
            @endphp

            @if($subItems->count() > 0)
                @foreach($subItems->sortBy('row_index') as $i => $subData)
                    <p><strong>Item {{ $i + 1 }}</strong></p>
                    <table class="border-none">
                        @foreach($subDesign->fields as $subField)
                            @php
                                $v = $subData->data[$subField->name] ?? null;
                            @endphp
                            <tr class="field">
                                <td class="font-wrap">{{ $subField->label }}:</td>
                                <td class="text-bold">@include('exports.partials.pdf-field-display', ['field' => $subField, 'value' => $v])</td>
                            </tr>
                        @endforeach
                    </table>
                @endforeach
            @else
                <p class="no-data">Tidak ada data untuk sub form ini.</p>
            @endif
        </div>
    @endforeach

    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
