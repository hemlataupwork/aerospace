<?php


class regionalpoc_table extends table_sql
{
  function __construct($uniqueid)
  {
    parent::__construct($uniqueid);
    
    $roleid = optional_param('roleid', 0, PARAM_INT);
    // var_dump($roleid);
    // die;
      if($roleid==3){
        $columns = array('serialno', 'fullname', 'contact',   'appointment', 'edit');
        $this->define_columns($columns);
        
        $headers = array('S.No', 'Fullname', 'Contact',  'Assigned ARM','Action');
        $this->define_headers($headers);
        }
      elseif($roleid==4){
        $columns = array('serialno', 'fullname', 'contact',  'edit');
        $this->define_columns($columns);

        $headers = array('S.No', 'Fullname', 'Contact',  'Action');
        $this->define_headers($headers);
      }
    }
    
    function col_edit($values)
    {
      echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />';
      // var_dump($values);
      // die;
      $roleid = optional_param('roleid', 0, PARAM_INT);
      // var_dump($roleid);
      // die;
      if($roleid==4){
        global $CFG;
        $button_html = "<a href='$CFG->wwwroot/local/armtoschool/school.php?userId=$values->id'  title='Assign ' class='btn btn-primary'><i class='fa-solid fa-school-circle-check'></i></a> | <a href='$CFG->wwwroot/local/regionalpoc/edit_regionalpoc_form.php?id=$values->id' class='btn btn-primary' title='Edit '><i class='fa fa-cog'></i></a> | <a href='$CFG->wwwroot/local/regionalpoc/delete_regionalpoc.php?id=$values->id&userid=$values->userid' class='btn btn-primary' title='Delete'><i class='fa fa-trash'></i></a>";
        return $button_html;
      } elseif($roleid==3){
        global $CFG;
        $button_html = "<a href='$CFG->wwwroot/local/regionalpoc/assign_arm_to_rm.php?rmid=$values->id' class='btn btn-primary' title='Assign '><i class='fa-solid fa-user-check'></i></a> | <a href='$CFG->wwwroot/local/regionalpoc/edit_regionalpoc_form.php?id=$values->id' class='btn btn-primary' title='Edit '><i class='fa fa-cog'></i></a> | <a href='$CFG->wwwroot/local/regionalpoc/delete_regionalpoc.php?id=$values->id&userid=$values->userid' class='btn btn-primary' title='Delete'><i class='fa fa-trash'></i></a>";
        return $button_html;
      }
    }

    

    function col_appointment($values)
{
    global $DB;
    // var_dump($values);die;
    $button_html = "<button type='button' class='btn btn-primary' onclick='openModal(\"appointmentModal$values->userid\")'>View</button>";

    $modal_html = "
    <div id='appointmentModal$values->userid' class='modal'>
      <div class='modal-content'>
        <span class='close' onclick='closeModal(\"appointmentModal$values->userid\")'>&times;</span>
        <table class='table'>
          <thead>
            <tr>
              <th>Serial No</th>
              <th>ARM Names</th>
              <th>Assiged Schools</th>
            </tr>
          </thead>
          <tbody>";

    // Adding ARM names to the table rows
    $armNames=$DB->get_records_sql("SELECT ar.id as id, ar.username as name from {regionalpoc} ar join {assigned_arm} aaa on ar.id = aaa.armid where aaa.rmid = $values->id");
    // var_dump($armNames);
    // // die;
    $index=1;
    foreach ($armNames as  $armName) {
      $schools=$DB->get_record_sql("SELECT count(schoolid) as count from {schoolassign} where userid=$armName->id");
      foreach($schools as $sch1){

        $modal_html .= "
            <tr>
              <td>" . ($index) . "</td>
              <td>$armName->name</td>
              <td>$sch1</td>
            </tr>";
            $index+=1;
      }
    }

    $modal_html .= "
          </tbody>
          <tfoot>
              <tr>
                <td colspan='3' style='text-align: center;'>
                  <button type='button' class='btn btn-secondary' onclick='closeModal(\"appointmentModal$values->userid\")'>Close</button>
                </td>
              </tr>
            </tfoot>
        </table>
      </div>
    </div>
    ";

    $style_html = "
    <style>
      .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
      }

      .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 50%; /* Could be more or less, depending on screen size */
      }

      .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
      }

      .close:hover,
      .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
      }

      .modal-body {
        text-align: center; /* Center table content */
      }

      table {
        width: 100%;
      }
    </style>
    ";
    

    return $button_html . $modal_html . $style_html ;
}

   
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
      function openModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = 'block';
      }

      function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = 'none';
      }

      // Close the modal if the user clicks anywhere outside of it
      window.onclick = function(event) {
        var modals = document.getElementsByClassName('modal');
        for (var i = 0; i < modals.length; i++) {
          var modal = modals[i];
          if (event.target == modal) {
            modal.style.display = 'none';
          }
        }
      }
    </script>