$(function() {
    $('#search-published-date').datepicker();

    // Display correct sort icon upon hovering, default is "Asc"
    $('.sort-link').on('click', function() {
        var current_sort = $(this).attr('data-sort-order');
        var column = $(this).attr('data-col');
        var sort_form = document.frmSortBlogs;

        // Clear the data-col, data-sort-order values of other columns
        
        // Determine the sort order
        if (current_sort === 'Desc' || current_sort === '')
        {
            // Asc
            $(this).html($(this).html() + ' Asc');
        }
        else
        {
            // Desc
            $(this).html($(this).html() + ' Desc');
        }

        // Submit
        sort_form.submit();
    });
});