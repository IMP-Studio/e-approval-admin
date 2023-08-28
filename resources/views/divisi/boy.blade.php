
@extends('layouts.master')
@push('css')
    <link href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.22.1/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.min.css" rel="stylesheet">
    <style>
        .toolbar{
            margin-top: 350px;
        }
        table{
            margin-top: 200px;
        }
    </style>
@endpush
	@section('content')
    <div class="toolbar">
        <div>
          <label class="checkbox">
            <input id="height" type="checkbox" checked> Enable Height
          </label>
        </div>
        <div class="form-inline">
          <span class="mr10">Fixed Number: </span>
          <input class="form-control mr10" id="fixedNumber" type="number" value="2" min="1" max="5">
          <span class="mr10">Fixed Right Number: </span class="mr10">
          <input class="form-control" id="fixedRightNumber" type="number" value="1" min="0" max="5">
        </div>
        <div class="form-inline">
          <span class="mr10">Cells: </span>
          <input class="form-control mr10" id="cells" type="number" value="20" min="1" max="30">
          <span class="mr10">Rows: </span class="mr10">
          <input class="form-control mr10" id="rows" type="number" value="20" min="1" max="50">
          <button id="build" class="btn btn-secondary">Rebuild Table</button>
        </div>
      </div>

      <table id="table"></table>
	<!--/container-->
    @endsection

@push('js')
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/extensions/fixed-columns/bootstrap-table-fixed-columns.min.js"></script>
    <script>
        var $table = $('#table')

        function buildTable($el) {
        var cells = +$('#cells').val()
        var rows = +$('#rows').val()
        var i
        var j
        var row
        var columns = [
            {
            field: 'state',
            checkbox: true,
            valign: 'middle'
            }
        ]
        var data = []

        for (i = 0; i < cells; i++) {
            columns.push({
            field: 'field' + i,
            title: 'Cell' + i,
            sortable: true,
            valign: 'middle',
            formatter: function (val) {
                return '<div class="item">' + val + '</div>'
            },
            events: {
                'click .item': function () {
                console.log('click')
                }
            }
            })
        }
        for (i = 0; i < rows; i++) {
            row = {}
            for (j = 0; j < cells + 3; j++) {
            row['field' + j] = 'Row-' + i + '-' + j
            }
            data.push(row)
        }
        $el.bootstrapTable('destroy').bootstrapTable({
            height: $('#height').prop('checked') ? 400 : undefined,
            columns: columns,
            data: data,
            search: true,
            showColumns: true,
            showToggle: true,
            clickToSelect: true,
            fixedColumns: true,
            fixedNumber: +$('#fixedNumber').val(),
            fixedRightNumber: +$('#fixedRightNumber').val()
        })
        }

        $(function() {
        buildTable($table)

        $('#build').click(function () {
            buildTable($table)
        })
        })
    </script>
@endpush
