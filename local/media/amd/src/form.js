window.addEventListener("load", () => {
    console.log("HI")
    const school = document.getElementById("id_school");
    const baseUrl = window.location.origin + "/" + window.location.pathname.split("/")[1];
    
    console.log(baseUrl)
    school.addEventListener('change', function() {
        const data = {};
        data.id = this.value;
        $.ajax({
            url: baseUrl + "/local/media/test.php",
            method: "post",
            data,
            dataType: "json",
            async: true,
            success: function (resp) {
                console.log(resp);
                $("#id_subcategory").html(resp)
                $("#id_subcategory1").html(resp)
              if (resp.success){
                console.log("Ajax run successfully");
              }
            },
            error: function (xhr, status, error) {
              console.log("Error:", error);
            },
          });
    });
});