$(function() {
    // Datepicker
    $('#search-published-date').datepicker();

    // Column sorting
    $('.sort-link').on('click', function() {
        var sort_form = document.frmSortBlogs;
        var current_sort = sort_form.sd;
        var column = $(this).attr('data-col');
        sort_form.sb.value = column;

        // Determine the sort order
        if (current_sort.value === 'Desc' || current_sort.value === '')
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
        sort_form.submit();
    });
});