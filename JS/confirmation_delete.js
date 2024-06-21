$(document).ready(function () {

    const modal = new bootstrap.Modal($('#confirmationDelete')[0]);
    const confirmExitButton = $('#confirmExitButton');
    const backButton = $('.back'); 

    $('.delete').click(function (event) {
        event.preventDefault(); 
        modal.show();
    });

    confirmExitButton.click(function () {
    
        $.ajax({
            url: $('#deleteForm').attr('action'), 
            type: 'POST', 
            data: $('#deleteForm').serialize(), 
            success: function (response) {
                modal.hide();
                $('#confirmationDelete').on('hidden.bs.modal', function () {
                    const confirm = new bootstrap.Modal($("#isDeleted")[0]);
                    confirm.show();
                }); 
            },
            error: function (xhr, status, error) {
            
                console.error(error);
               
            }
        });
    });
});