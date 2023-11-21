$(document).ready(function () {
    $('#groupsTab').on('click', function (e) {
        activeGroupsList();
    });

    $('#body').on('click', '#addGroupBtn', function (e) {
        e.preventDefault();
        openGroupForm(null);
    })

    function activeGroupsList() {
        $('#body').innerHTML= '';
        $('#usersTab').removeClass('active');
        $('#groupsTab').addClass('active');
        $('#body').load('src/public/templates/group/list.html');
        fetchGroups();
    }

    $('#body').on('click', '.deleteGroup', function () {
        var groupId = $(this).closest('tr').data('groupid');
        if (confirm('Are you sure you want to delete this group?')) {
            deleteGroup(groupId);
        }
    });

    $('#body').on('click', '.editGroup', function () {
        var groupId = $(this).closest('tr').data('groupid');
        openGroupForm(groupId);
    });

    function deleteGroup(groupId) {
        $.ajax({
            url: 'http://localhost:8000/api/groups/' + groupId,
            method: 'DELETE',
            dataType: 'json',
            success: function(response) {
                showToast('Successfully deleted group.', false);
                activeGroupsList();
            },
            error: function(xhr, status, error) {
                showToast('Error deleting group.', false);
                console.error('Error deleting group:', status, error);
            }
        });
    }

    $('#body').on('click', '.groupDetail', function (e) {
        e.preventDefault();
        var groupId = $(this).closest('tr').data('groupid');
        openGroupDetails(groupId);
    });

    function openGroupDetails(groupId) {
        $('#body').empty();
        $('#body').load('src/public/templates/group/detail.html', function () {
            fetchGroupDetail(groupId);
        });
    }

        function fetchGroupDetail(groupId, action) {
            $.ajax({
                url: 'http://localhost:8000/api/groups/' + groupId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (action === 'form') {
                        fillGroupForm(response.results);
                    } else {
                        displayGroupDetail(response.results);
                        addUserDataToButtons(groupId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching group details:', status, error);
                }
            });
        }

        function displayGroupDetail(group) {
            $('#groupDetail .name').text('Name: ' + group.name);
            displayGroupUsers(group.users);
        }

    function addUserDataToButtons(groupId) {
        $('#deleteGroup').data('groupid', groupId);
        $('#editGroup').data('groupid', groupId);
    }

    $('#body').on('click', '#editGroup', function (e) {
        e.preventDefault();
        const groupId = $('#editGroup').data('groupid');
        openGroupForm(groupId);
    })

    $('#body').on('click', '#deleteGroup', function (e) {
        e.preventDefault();
        const groupId = $('#deleteGroup').data('groupid');
        deleteGroup(groupId);
    })

    function fillGroupForm(group) {
        $('#name').val(group.name);

        if (group.users && group.users.length > 0) {
            var selectedUsers = group.users.map(function(user) {
                return user.id;
            });

            $('#users').val(selectedUsers);
        }
    }

    function openGroupForm(groupId) {
        $('#body').empty();
        $('#body').load('src/public/templates/group/form.html', async function () {
            await fetchAndDisplayUsers();
            if (groupId != null) {
                fetchGroupDetail(groupId, 'form');
                $('#saveGroup').data('groupid', groupId);
            }
        });
    }

    $('#body').on('click', '#saveGroup', function (e) {
        const groupId = $('#saveGroup').data('groupid');

        saveGroup(groupId)
    })

    function saveGroup(groupId)
    {
        var url = 'http://localhost:8000/api/groups/';

        if (groupId !== undefined) {
            url += groupId;
        }

        $.ajax({
            url: url,
            method: groupId !== undefined ? 'PUT' : 'POST',
            dataType: 'json',
            data: JSON.stringify({
                name: $('#name').val(),
                users: $('#users').val() === null || $('#users').val() === undefined ? [] : $('#users').val()
            }),
            success: function(response) {
                showToast('Successfully saved group.', false);
                openGroupDetails(response.results.id)
            },
            error: function(xhr, status, error) {
                showToast('An error occurred during saving group.', true);
                console.error('Error saving groups:', status, error);
            }
        });
    }

    function fetchAndDisplayUsers() {
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

    function displayUsers(users) {
        var groupsSelect = $('#groupUsers');

        if (users && users.length > 0) {
            groupsSelect.append('<select class="form-select" name="users" id="users" multiple>');

            users.forEach(function (user) {
                groupsSelect.find('select').append(`<option value="${user.id}">${user.name} ${user.surname} (${user.username})</option>`);
            });

            groupsSelect.append('</select>');
        } else {
            groupsSelect.append('<p>No users available.</p>');
        }
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

        function displayGroupUsers(users) {
            var userGroupsContainer = $('#groupUsers');
            userGroupsContainer.empty();

            if (users && users.length > 0) {
                userGroupsContainer.append('<h3>Group Users:</h3>');
                userGroupsContainer.append('<ul>');
                users.forEach(function (user) {
                    userGroupsContainer.append(`<li>${user.name} - ${user.surname}</li>`);
                });
                userGroupsContainer.append('</ul>');
            } else {
                userGroupsContainer.append('<p>No users.</p>');
            }
        }

    function fetchGroups() {
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
        var groupsList = $('#groupsListTableBody');
        groupsList.empty();
        groups.forEach(function (group) {
            groupsList.append(`
                <tr data-groupid="${group.id}">
                    <td class="p-2">#${group.id}</td>
                    <td class="p-2">${group.name}</td>
                    <td class="actions">
                        <span class="deleteGroup action-btn danger">Delete</span>
                        <span class="groupDetail action-btn info">Show Details</span>
                        <span class="editGroup action-btn warning">Edit</span>
                    </td>
                </tr>`
            );
        });
    }
})