$(document).ready(function () {
    const degreeID = document.getElementById('degreeID').value;

    $.ajax({
        url: "/9.0/degree/get",
        type: 'GET',
        data: {degreeID: degreeID},
        dataType: "json",
        success: function (res) {
            const city = res['data']['degree']['city'];
            const country = res['data']['degree']['country'];
            const end_date = res['data']['degree']['end_date'];
            const start_date = res['data']['degree']['start_date'];
            const fieldOfStudy = res['data']['degree']['fieldOfStudy'];
            const name = res['data']['degree']['name'];
            const university = res['data']['degree']['university'];

            document.getElementById("city").value = city;
            document.getElementById("country").value = country;
            document.getElementById("end_date").value = end_date;
            document.getElementById("start_date").value = start_date;
            document.getElementById("field_of_study").value = fieldOfStudy;
            document.getElementById("name").value = name;
            document.getElementById("university").value = university;
        },
        error: function (res) {
            console.log(res);
            alert('Could not fetch data!');
        }
    });
});
