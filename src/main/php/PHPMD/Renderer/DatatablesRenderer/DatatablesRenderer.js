$(document).ready(function () {
    
    function format(d)
    {
        return d[6];
    }
    
    var table = $('#phpmdDatatable').DataTable(
        {
            columnDefs: [
                {
                    targets       : 0,
                    className     : 'details-control',
                    orderable     : false,
                    data          : null,
                    defaultContent: ''
                },
                {
                    targets: [6],
                    visible: false
                }
            ],
            pageLength: 100
        }
    );
    
    $('#phpmdDatatable tbody').on('click', 'td.details-control', function () {
        var tr  = $(this).closest('tr');
        var row = table.row(tr);
        
        if(row.child.isShown())
        {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else
        {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });
    
    
});