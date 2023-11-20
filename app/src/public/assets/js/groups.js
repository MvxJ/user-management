$(document).ready(function () {
    $('#groupsTab').on('click', function (e) {
        e.preventDefault();
        fetchGroups();
        showGroupsView();
    });

    function showGroupsView() {
        $('#usersView').hide();
        $('#groupsView').show();
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
        var groupList = $('#groupList');
        groupList.empty();
        groups.forEach(function (group) {
            groupList.append('<li>' + group.name + '</li>');
        });
    }
})