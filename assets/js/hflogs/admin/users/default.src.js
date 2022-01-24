var adminUsers = {
    updateUserType: function(user_id, type_id) {
        return $.post('/admin_users/type/update', {'user_id':user_id, 'type_id':type_id}, null, 'json');
    },
    removeUser: function(user_id) {
        return $.post('/admin_users/task/remove', {id:user_id}, null, 'json');
    }
};

$(function() {
    $(document).on('click', '.removeUser', function(e) {
        e.preventDefault();

        var r = confirm('Are you sure you want to permanently remove this user?');
        if(r == true) {
            $.when(adminUsers.removeUser($(e.currentTarget).data('id'))).done(function(a) {
                $(e.currentTarget).parent().parent().parent().fadeOut('fast', function() {
                    $(this).remove();
                });
            });
        }
    });

    $(document).on('click', '.disableUser', function(e) {
        e.preventDefault();

        var href = $(this).data('href');

        window.location.href = href;
    });

    $(document).on('change', '.userTypeSel', function(e) {
        var user_id = $(this).data('userid');
        var type_id = $(this).val();

        $.blockUI({message:'Updating user type...'});

        $.when(adminUsers.updateUserType(user_id, type_id)).done(function(a) {
            $.unblockUI();
        });
    });
});