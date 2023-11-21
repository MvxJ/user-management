$(document).ready(function () {
    activeUserList();

    $('#usersTab').on('click', function (e) {
        activeUserList();
    });

    function activeUserList() {
        $('#body').innerHTML= '';
        $('#usersTab').addClass('active');
        $('#groupsTab').removeClass('active');
        $('#body').load('src/public/templates/user/list.html');
        fetchUsers();
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

    function fetchUserDetails(userId, action) {
        $.ajax({
            url: 'http://localhost:8000/api/users/' + userId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (action === 'form') {
                    fillUserForm(response.results);
                } else {
                    displayUserDetails(response.results);
                    addUserDataToButtons(userId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching user details:', status, error);
            }
        });
    }

    function displayUserDetails(user) {
        var birthDate = user.birthDate ? new Date(user.birthDate.date).toLocaleDateString() : '';

        $('#userDetail .name').text('Name: ' + user.name);
        $('#userDetail .surname').text('Surname: ' + user.surname);
        $('#userDetail .username').text('Username: ' + user.username);
        $('#userDetail .birthDate').text('BirthDate: ' + birthDate);

        displayUserGroups(user.groups);
    }

    function fillUserForm(user) {
        $('#name').val(user.name);
        $('#surname').val(user.surname);
        $('#username').val(user.username);
        var datePart = user.birthDate.date.split(' ')[0];
        $('#birthDate').val(datePart);
        $('#password').val(user.password);

        if (user.groups && user.groups.length > 0) {
            var selectedGroups = user.groups.map(function(group) {
                return group.id;
            });
            $('#groups').val(selectedGroups);
        }
    }

    function displayUserGroups(groups) {
        var userGroupsContainer = $('#userGroups');
        userGroupsContainer.empty();

        if (groups && groups.length > 0) {
            userGroupsContainer.append('<h3>User Groups:</h3>');
            userGroupsContainer.append('<ul>');
            groups.forEach(function (group) {
                userGroupsContainer.append('<li>' + group.name + '</li>');
            });
            userGroupsContainer.append('</ul>');
        } else {
            userGroupsContainer.append('<p>User is not part of any groups.</p>');
        }
    }

    $('#body').on('click', '.deleteUser', function () {
        var userId = $(this).closest('tr').data('userid');
        if (confirm('Are you sure you want to delete this user?')) {
            deleteUser(userId);
        }
    });

    $('#body').on('click', '.userDetail', function (e) {
        e.preventDefault();
        var userId = $(this).closest('tr').data('userid');
        openUserDetails(userId);
    });


    $('#body').on('click', '.userEdit', function (e) {
        e.preventDefault();
        var userId = $(this).closest('tr').data('userid');
        openUserForm(userId);
    });

    $('#body').on('click', '#addUserBtn', function (e) {
        e.preventDefault();
        openUserForm(null);
    })

    function openUserDetails(userId) {
        $('#body').empty();
        $('#body').load('src/public/templates/user/detail.html', function () {
            fetchUserDetails(userId);
        });
    }

    function addUserDataToButtons(userId) {
        $('#editUser').data('userid', userId);
        $('#deleteUser').data('userid', userId);
    }

    $('#body').on('click', '#editUser', function (e) {
        e.preventDefault();
        const userId = $('#editUser').data('userid');
        openUserForm(userId);
    })

    $('#body').on('click', '#deleteUser', function (e) {
        e.preventDefault();
        const userId = $('#deleteUser').data('userid');
        deleteUser(userId);
    })

    function openUserForm(userId) {
        $('#body').empty();
        $('#body').load('src/public/templates/user/form.html', async function () {
            await fetchAndDisplayGroups();
            if (userId != null) {
                $('#saveUser').data('userid', userId);
                fetchUserDetails(userId, 'form');
                $('#userAddForm').hide();
            } else {
                $('#userEditForm').hide();
            }
        });
    }

    $('#body').on('click', '.deleteDetails', function () {
        var userId = $(this).data('userid');
        if (confirm('Are you sure you want to delete this user?')) {
            deleteUser(userId);
            activeUserList();
        }
    });

    function deleteUser(userId) {
        $.ajax({
            url: 'http://localhost:8000/api/users/' + userId,
            method: 'DELETE',
            dataType: 'json',
            success: function(response) {
                showToast('Successfully deleted user.', false);
                activeUserList();
            },
            error: function(xhr, status, error) {
                showToast('An error occurred during deleting user.', true);
                console.error('Error deleting user:', status, error);
            }
        });
    }

    function fetchAndDisplayGroups() {
        $.ajax({
            url: 'http://localhost:8000/api/groups',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                displayGroups(response.results);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching groups:', status, error);
            }
        });
    }

    function displayGroups(groups) {
        var groupsSelect = $('#userGroups');

        if (groups && groups.length > 0) {
            groupsSelect.append('<select class="form-select" name="groups" id="groups" class="form-select" multiple>');

            groups.forEach(function (group) {
                groupsSelect.find('select').append('<option value="' + group.id + '">' + group.name + '</option>');
            });

            groupsSelect.append('</select>');
        } else {
            groupsSelect.append('<p>No groups available.</p>');
        }
    }

    $('#body').on('click', '#saveUser', function (e) {
        const userId = $('#saveUser').data('userid');
        saveUser(userId);
    });

    function saveUser(userId)
    {
        var requestData = {
            name: $('#name').val(),
            surname: $('#surname').val(),
            username: $('#username').val(),
            birthDate: $('#birthDate').val(),
            groups: $('#groups').val() === null || $('#users').val() === undefined ? [] : $('#groups').val()
        }

        if (userId === undefined) {
            requestData.password = $('#password').val();
        } else {
            if ($('#oldPassword').val().length > 0 && $('#newPassword').val().length > 0) {
                requestData.newPassword = $('#newPassword').val();
                requestData.oldPassword = $('#oldPassword').val();
            }
        }

        var url = 'http://localhost:8000/api/users';

        if (userId !== undefined) {
            url += "/" + userId;
        }

        $.ajax({
            url: url,
            method: userId !== undefined ? 'PUT' : 'POST',
            dataType: 'json',
            data: JSON.stringify(requestData),
            success: function(response) {
                showToast('Successfully saved user.', false);
                openUserDetails(response.results.id);
            },
            error: function(response, xhr, status, error) {
                showToast('Error during saving user.', true);
                console.error('Error during saving user:', status, error);
            }
        });
    }

    function showToast(message, isError = false) {
        var toastElement = $('#liveToast');
        var toastMessageElement = $('#toastMessage');

        toastMessageElement.text(message);
        toastElement.removeClass('error');

        if (isError) {
            toastElement.addClass('error');
        }

        toastElement.toast('show');
    }

    function displayUsers(users) {
        var userList = $('#usersListTableBody');
        userList.empty();
        users.forEach(function (user) {
            userList.append(`
                <tr data-userid="${user.id}">
                    <td class="p-2">#${user.id}</td>
                    <td class="p-2">${user.name}</td>
                    <td class="p-2">${user.surname}</td>
                    <td class="p-2">${user.username}</td>
                    <td class="actions">
                        <span class="deleteUser action-btn danger">Delete</span>
                        <span class="userDetail action-btn info">Show Details</span>
                        <span class="userEdit action-btn warning">Edit</span>
                    </td>
                </tr>`
            );
        });
    }
});

