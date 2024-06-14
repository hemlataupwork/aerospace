<?php

defined('MOODLE_INTERNAL') || die();


class image_list extends table_sql {

    
  
    function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $columns = array('serialno','school_name','class','name','action');
        $this->define_columns($columns);

        $headers = array('S.No','School name','Class','File Name','Action');
        $this->define_headers($headers);
    }

    function col_timecreated($values) {
       
            $times=date("d-m-Y", $values->timecreated);
            return $times;  
        
    }
    // function col_action($values)
    // {
    //     global $CFG;
    //     $button_html = "<a href='$CFG->wwwroot/local/media/delete_act_image.php?id=$values->id' class='btn btn-primary'><i class='fa fa-trash'></i></a>";
    //     return $button_html;
    // }

    function col_action($values)
    {
        global $CFG;
        $button_html = "<button class='btn btn-primary' onclick='deleteImage($values->imageid)'><i class='fa fa-trash'></i></button>";
        return $button_html;
    }
    function define_headers($headers) {
        parent::define_headers($headers);
        $this->no_sorting('action');
    }
}

?>

<script>
function deleteImage(imageid) {
    if (confirm('Are you sure you want to delete this record?')) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    location.reload();
                } else {
                    alert('Error occurred while deleting the record.');
                }
            }
        };
        xhr.open('POST', '<?php echo $CFG->wwwroot; ?>/local/media/delete_act_image.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('id=' + imageid);
    }
}
</script>

