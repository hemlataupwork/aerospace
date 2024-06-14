document.addEventListener('DOMContentLoaded', function () {
    const addSelect = document.getElementById('addselect');
    const removeSelect = document.getElementById('removeselect');
    const addBtn = document.getElementById('add');
    const removeBtn = document.getElementById('remove');
    const schoolSelect = document.getElementById('id_armid');
    var armid = document.getElementById('armid');

    addBtn.addEventListener('click', () => {
        armid.setAttribute('value', armSelect.value);
    });

    removeBtn.addEventListener('click', () => {
        armid.setAttribute('value', armSelect.value);
    });

    addSelect.addEventListener('change', () => {
        addBtn.disabled = addSelect.selectedOptions.length === 0;
    });

    removeSelect.addEventListener('change', () => {
        removeBtn.disabled = removeSelect.selectedOptions.length === 0;
    });

    schoolSelect.addEventListener('change', function () {
        const selectedValue = this.value;
        if (selectedValue != 0) {
            fetchExistingUsers(selectedValue);
        }
    });

    function clearSearch(inputId) {
        document.getElementById(inputId).value = '';
    }

    function fetchExistingUsers(armid) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_existing_arm.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const users = JSON.parse(xhr.responseText);
                updateRemoveSelect(users);
            }
        };
        xhr.send('armid=' + armid);
    }

    function updateRemoveSelect(users) {
        removeSelect.innerHTML = '';
        users.forEach(user => {
            const option = document.createElement('option');
            option.value = user.id;
            option.textContent = user.username;
            removeSelect.appendChild(option);
        });
    }
});