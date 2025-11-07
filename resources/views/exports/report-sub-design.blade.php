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
                        <td>
                            {{ $row->data[$field->name] ?? '' }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
