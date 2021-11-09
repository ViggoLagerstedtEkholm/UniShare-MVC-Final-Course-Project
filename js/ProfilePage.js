window.onload = function () {
    let projectButton = document.getElementById("projectTab");
    let degreeButton = document.getElementById("degreeTab");

    projectButton.addEventListener('click', function (event) {
        openTab(event, 'Projects');
    });

    degreeButton.addEventListener('click', function (event) {
        openTab(event, 'Degrees');
    });

    //Go to projects tab by default.
    openTab(event, 'Projects');
}

function openTab(evt, tabName) {
    let i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("profile-tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

$(document).ready(function () {
    $('#addCommentForm').on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "./profile/add/comment",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (res) {
                location.reload();
            },
            error: function (xhr, res) {
                if (xhr.status === 403) {
                    alert('You need to be logged in to comment!');
                }
                if (xhr.status === 500) {
                    alert('Your comment is empty.');
                }
            }
        });
    });
});

function deleteCourseInDegree(degreeID, courseID) {
    $.ajax({
        url: "./profile/delete/course",
        type: "POST",
        data: {courseID: courseID, degreeID: degreeID},
        dataType: "json",
        success: function (res) {
            console.log(res);
            const canRemove = res["data"]["Status"];
            if (canRemove) {
                const ID = res["data"]["ID"];
                document.getElementById("course-" + ID).remove();
            }
        },
        error: function (res) {
            console.log(res);
        }
    });
}

function deleteDegree(ID) {
    $.ajax({
        url: "/9.0/degree/remove",
        type: "POST",
        data: {degreeID: ID},
        dataType: "json",
        success: function (res) {
            console.log(res);
            const canRemove = res["success"];
            if (canRemove) {
                const ID = res["data"]["degreeID"];
                document.getElementById("degree-" + ID).remove();
            }
        },
        error: function (res) {
            console.log(res);
        }
    });
}

function deleteProject(ID) {
    $.ajax({
        url: "/9.0/project/delete",
        type: "POST",
        data: {projectID: ID},
        dataType: "json",
        success: function (res) {
            console.log(res);
            const canRemove = res["data"]["Status"];
            if (canRemove) {
                const ID = res["data"]["ID"];
                document.getElementById("project-" + ID).remove();
            }
        },
        error: function (res) {
            console.log(res);
        }
    });
}

function deleteComment(ID) {
    $.ajax({
        url: "./profile/delete/comment",
        type: "POST",
        data: {commentID: ID},
        dataType: "json",
        success: function (res) {
            console.log(res);
            const canRemove = res["data"]["Status"];
            if (canRemove) {
                const ID = res["data"]["ID"];
                document.getElementById(ID).remove();
            }
        },
        error: function (res) {
            console.log(res);
        }
    });
}