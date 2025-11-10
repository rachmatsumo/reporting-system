<h3>{{ $subDesign->name }}</h3>

<table>
    <thead>
        <tr>
            <th>Report ID</th>
            <th>Report Title</th>

            @foreach($subDesign->fields as $field)
                <th>{{ $field->label }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($reports as $report)
            @php
                $rows = $report->subData
                    ->where('report_sub_design_id', $subDesign->id)
                    ->sortBy('row_index');
            @endphp

            @foreach($rows as $row)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>{{ $report->reportDesign->name }}</td>

                    @foreach($subDesign->fields as $field)
                        @php $v = $row->data[$field->name] ?? null; @endphp
                        <td>
                            {{-- Jika tanda tangan --}}
                            @if($field->type === 'signing' && $v)
                                (Tanda Tangan)
                            
                            {{-- Jika field berupa file atau gambar --}}
                            @elseif(in_array($field->type, ['image', 'file']) && $v)
                                <a href="{{ asset($v) }}" target="_blank">Lampiran</a>

                            {{-- Jika field berupa array --}}
                            @elseif(is_array($v))
                                {{ json_encode($v, JSON_UNESCAPED_UNICODE) }}

                            {{-- Nilai biasa --}}
                            @else
                                {{ $v }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
