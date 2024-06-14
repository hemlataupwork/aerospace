window.addEventListener('load', () => {
    const category_select = document.querySelector(".custom-select");
    const subcategory_select = document.querySelector("#id_subcategory1");
    category_select.addEventListener('change', function () {
        $.post("ajax.php", {
            categoryid: category_select.value
        }, function (res) {
            const data = JSON.parse(res);
            subcategory_select.innerHTML = '';
            data.data.forEach(element => {
                let option = document.createElement("option");
                option.value = element.id;
                option.innerText = element.name;
                subcategory_select.add(option);

            });
        }
        )
    })
})
