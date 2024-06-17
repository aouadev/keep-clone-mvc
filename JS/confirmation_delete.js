$(document).ready(function () {

    const modal = new bootstrap.Modal(document.getElementById('confirmationDelete'));
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
                console.log("success"); 
            },
            error: function (xhr, status, error) {
            
                console.error(error);
               
            }
        });
    });
});