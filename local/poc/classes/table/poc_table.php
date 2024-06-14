<?php

class poc_table extends table_sql
{
    function __construct($uniqueid)
    {
        parent::__construct($uniqueid);

        $columns = array('serialno', 'fullname', 'contact',  'designation', 'appointment', 'edit');
        $this->define_columns($columns);

        $headers = array('S.No', 'Fullname', 'Contact',  'Department', 'Appointed School', 'Edit');
        $this->define_headers($headers);
    }

    function col_appointment($values)
    {
        global $CFG;
        $button_html = "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#appointmentModal$values->userid' data-id='$values->userid'>View</button>";

        $modal_html = "
        <div class='modal fade' id='appointmentModal$values->userid' tabindex='-1' role='dialog' aria-labelledby='appointmentModalLabel$values->userid' aria-hidden='true'>
            <div class='modal-dialog' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='appointmentModalLabel$values->userid'>Appointed School Details</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <p>hi</p>
                        <!-- Content to be loaded dynamically -->
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                    </div>
                </div>
            </div>
        </div>";

        return $button_html . $modal_html;
    }

    function col_edit($values)
    {
        global $CFG;
        $button_html = "<a href='$CFG->wwwroot/local/poc/edit_poc_form.php?id=$values->id' class='btn btn-primary' title='Edit POC Details'><i class='fa fa-cog'></i></a> | <a href='$CFG->wwwroot/local/poc/delete_poc.php?id=$values->id&userid=$values->userid' class='btn btn-primary' title='Delete POC'><i class='fa fa-trash'></i></a>";
        return $button_html;
    }
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('[data-toggle="modal"]').on('click', function (event) {
        var button = $(this);
        var id = button.data('id');
        var baseURL = '<?php echo $CFG->wwwroot; ?>/';
        // console.log(baseURL)

        // Use AJAX to fetch the data based on ID and update the modal content
        $.ajax({
            url: baseURL+'local/poc/classes/table/fetch_user_data.php',
            method: 'GET',
            data: { id: id },
            success: function(response) {
                $('#appointmentModal' + id + ' .modal-body').html(response);
            },
            error: function() {
                $('#appointmentModal' + id + ' .modal-body').html('<p>Error fetching data</p>');
            }
        });
    });
});
</script>
