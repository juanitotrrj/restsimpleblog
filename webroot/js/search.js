$(function() {
    // Datepicker
    $('#search-published-date').datepicker();

    // Column sorting
    $('.sort-link').on('click', function() {
        var sort_form = document.frmSortBlogs;
        var current_sort = sort_form.sd;
        var current_col = sort_form.sb;
        var column = $(this).attr('data-col');
        //console.log(current_sort.value, current_col.value);
        //console.log('-');

        // Determine the sort order
        if (column != current_col.value)
        {
            current_sort.value = '';
        }

        // Assign the column to sort
        sort_form.sb.value = column;

        if (current_sort.value == 'desc' || current_sort.value == '')
        {
            // Asc
            current_sort.value = 'asc';
        }
        else
        {
            // Desc
            current_sort.value = 'desc';
        }

        // Submit
        //console.log(current_sort.value, current_col.value);
        //console.log('='.repeat(30));
        sort_form.submit();
    });
});