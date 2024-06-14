<?php

class trainer_table extends table_sql
{


    function __construct($uniqueid)
    {
        parent::__construct($uniqueid);

        $columns = array('serialno','fullname', 'contact', 'designation', 'edit');
        $this->define_columns($columns);

        $headers = array('S.No', 'Fullname', 'Contact', 'Designation', 'Edit');
        $this->define_headers($headers);
    }


    function col_edit($values)
    {
        global $CFG;
        $button_html = "<a href='$CFG->wwwroot/local/trainer/edit_trainer_form.php?id=$values->id&userid=$values->userid' class='btn btn-primary' title='Edit Trainer'><i class='fa fa-pencil'></i></a>|<a href='$CFG->wwwroot/local/trainer/delete_trainer.php?id=$values->id&userid=$values->userid' class='btn btn-primary'><i class='fa fa-trash' title='Delete Trainer'></i></a>";
        return $button_html;
    }
    function define_headers($headers)
    {
        parent::define_headers($headers);
        $this->no_sorting('edit');
    }
}
