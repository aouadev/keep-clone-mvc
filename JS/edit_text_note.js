
let title, errTitle,error_add_note_php, description, errDescription, saveButton;


    
function checkTitle() {
    let ok = true;
    errTitle.html(""); 

    title.removeClass('is-valid is-invalid');

    if (title.val().length == 0) {
        errTitle.append("<p>Title is required.</p>");
      
        ok = false;
    } else if (title.val().length < minLength || title.val().length > maxLength) {
        errTitle.append("<p>Title length must be between " + minLength + " and " + maxLength + ".</p>");
        ok = false;
    }
    title.addClass(ok ? "is-valid" : "is-invalid");

    return ok;
}

function checkAll() {
    let ok = checkTitle();
    ok = ok && checkTextContent();
  
    return ok;
}


function checkTextContent() {
    errDescription.html("");
    description.removeClass('is-valid is-invalid');
    let ok = true;
    if (description.val().length > 0 && (description.val().length < minDescription || description.val().length > maxDescription)) {
        errDescription.append("Description must be between" + minDescription +" and " +  maxDescription );
        ok = false;
        description.addClass("form-control is-invalid");
    }
    else {
        description.addClass("form-control is-valid");
    }
    return ok;
}



function updateSaveButtonState() {
    isValid = checkTitleExists() && checkTitle() && checkTextContent();
    saveButton.prop('disabled', !isValid);
}


async function checkTitleExists() {
    let ok = true;
    error_add_note_php.html("");
    title.removeClass('is-valid is-invalid');
    const data = await $.getJSON("note/title_note_exists/" + id + "/" + title.val());
    if (data) {
        errTitle.append("<p>⚠already exists.</p>");
        ok = false;
        title.removeClass('is-valid');
        title.addClass("is-invalid");
}

return ok;
}
$(function() {
    title = $("#title");
    errTitle = $("#titleError");
    error_add_note_php = $(".error_add_note_php");
    description = $("#description");
    errDescription = $("#errDescription");
    saveButton = $("#saveButton");
    saveButton.prop('disabled', true);
    title.on("input", function() {
        checkTitle();
        updateSaveButtonState();
    });
  
    description.on("input", function() {
        checkTextContent();
        updateSaveButtonState();
    });
    $("input:text:first").focus();
  

});