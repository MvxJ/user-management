$(document).ready(function () {
    fetchUsers();

    $('#usersTab').on('click', function (e) {
        e.preventDefault();
        fetchUsers();
        showUsersView();
    });

    $('#userList').on('click', 'li', function(e) {
        e.preventDefault();
        var userId = $(this).data('userid');
        fetchUserDetails(userId);
    });

    function showUsersView() {
        $('#usersView').show();
        $('#groupsView').hide();
        $('#userDetails').hide();
    }

    function fetchUsers() {
        $.ajax({
            url: 'http://localhost:8000/api/users',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                displayUsers(response.results);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching users:', status, error);
            }
        });
    }

    function fetchUserDetails(userId) {
        $.ajax({
            url: 'http://localhost:8000/api/users/' + userId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                displayUserDetails(response.results);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching user details:', status, error);
            }
        });
    }

    function displayUserDetails(user) {
        $('#usersView').hide();
        $('#groupsView').hide();
        $('#userDetails').show();

        var userDetailsContent = $('#userDetailsContent');
        userDetailsContent.empty();
        userDetailsContent.append('<p>Name: ' + user.name + '</p>');
        userDetailsContent.append('<p>Surname: ' + user.surname + '</p>');
    }

    function displayUsers(users) {
        var userList = $('#userList');
        userList.empty();
        users.forEach(function (user) {
            userList.append('<li data-userid="' + user.id + '">' + user.name + " " + user.surname + '</li>');
        });
    }
});

