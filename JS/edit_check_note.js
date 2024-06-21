
let title, errTitle, error_add_note_php, saveButton, itemContent, error_items_empty, newItem, errNewItem;


    
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
   // ok = ok && await checkTitleExists();
  //  ok = ok && checkTextContent();
    ok = ok && checkItemUniqueness();
    return ok;
}






function checkItemsValidations() {
    let ok = true;
    let items = $("input[name='content[]']");
    let values = new Set();
    error_items_empty.html("");

    items.each(function() {
        let currentItem = $(this);
        let currentValue = currentItem.val();
        let errContainer = currentItem.nextAll('#errItem');
        errContainer.html("");
        if (currentValue !== "") {
            if (values.has(currentValue )) {
                errContainer.append("<p>⚠ Item must be unique.</p>");
                currentItem.removeClass('is-valid');
                currentItem.addClass("is-invalid");
                ok = false;
            
            }
            
            else {
                values.add(currentValue);
                currentItem.removeClass('is-invalid');
                currentItem.addClass("is-valid");
                ok = true;
                
            }
           
        }
    
         if ((id != 0  && (currentValue.length < minItemLength))  || (currentValue.length > maxItemLength)) {
                errContainer.append("<p>⚠ Item must be between "+ minItemLength+" and " + maxItemLength +".</p>");
                currentItem.removeClass('is-valid');
                currentItem.addClass("is-invalid");
                ok = false;
            }
        if (id == 0 && currentItem.val().length == 0 ) {
                currentItem.removeClass('is-valid');
        }

        
     //   currentItem.addClass(ok && currentValue != "" ? "is-valid" : currentValue == "" ? "" : "is-invalid");
       
    });
    if (values.size == 0) {
        error_items_empty.append("at least one item");
        ok = false
    }
    return ok;
}
function validateNewItem() {
    errNewItem.html("");
    let ok = true;
    let items = $("input[name='content[]']");
    let values = new Set();

    items.each(function() {
        values.add($(this).val());
    });
    if (values.has(newItem.val())) {
        errNewItem.append("<p>⚠ Item must be unique.</p>");
        newItem.removeClass('is-valid');
        newItem.addClass("is-invalid");
        ok = false;
    } else {
        newItem.removeClass('is-invalid');
        newItem.addClass("is-valid");
        ok = true;
        
    }
   if (newItem.val().length > maxItemLength) {
        errNewItem.append("<p>⚠ Item must be between "+ minItemLength +" and " + maxItemLength +".</p>");
        newItem.removeClass('is-valid');
        newItem.addClass("is-invalid");
        ok = false;
    }
    if (newItem.val().length == 0) {
        newItem.removeClass('is-valid');
        ok = true;
    }
    return ok;
    
}
function inContents() {
    content.forEach(element => {
        if (element['content'] == newItem.val()){
            return true;
        }
        

    });
    return false;
}

function updateSaveButtonState() {
    isValid = checkTitleExists() && checkTitle() && checkItemsValidations();
    saveButton.prop('disabled', !isValid);
}
function updateBtnAddItem() {
    isValid = validateNewItem();
    $('#btn_add_item').prop('disabled', !validateNewItem());
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
    saveButton = $("#saveButton");
    saveButton.prop('disabled', true);
    itemContent = $("input[name='content[]']");
    error_items_empty = $("#error_items_empty");
    newItem = $('#newItem');
    errNewItem = $('#errNewItem');
    newItem.bind("input", function() {
        validateNewItem();
       updateSaveButtonState();
       updateBtnAddItem();
    });

    title.on("input", function() {
        checkTitle();
        updateSaveButtonState();
    });

    itemContent.on("input", function() {
        checkItemsValidations();
        updateSaveButtonState();
    });

    $("input:text:first").focus();
  

});