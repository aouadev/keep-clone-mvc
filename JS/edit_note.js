
let title, errTitle,description, errDescription, errItem, itemContent, items;


    
function checkTitle() {
    let ok = true;
    errTitle.html("");
    if (title.val().length == 0) {
        errTitle.append("<p>Title is required.</p>");
        ok = false;
}

else if(title.val().length < minLength || title.val().length > maxLength) {
    errTitle.append("<p>Title length must be between 3 and 25.</p>");
    ok = false;
}

return ok;

}
function checkAll() {
    let ok = checkTitle();
    ok = ok && checkTitleExists(); // car le and est une évaluation parresseuse 
    ok = ok && checkTextContent(); // pour ne pas appeler les méthodes si ok est false
    ok = ok && errItem.val() == "";
    return ok;
}

function checkTextContent() {
    errDescription.html("");
    let ok = true;
    if (description.val().length > 0 && (description.val().length < minDescription || description.val().length > maxDescription)) {
        errDescription.append("Description must be between 5 and 800 car");
        ok = false;
    }
    return ok;
}



function checkUnicityItem() {
    errItem.html(""); // Clear previous errors
    let currentItemText = $(this).val();
    let isDuplicate = false;
    //console.log(currentItemText);
   for (let i = 0; i < itemContent.length; i++) {

    let currentValue = $(itemContent[i]).val();

  
    if (currentValue === currentItemText && itemContent[i] !== $(this)[0]) {
        isDuplicate = true;
        return false;
    }
}

    if (isDuplicate) {
        console.log("duplicate");
        errItem.append("<p>⚠ Item already exists.</p>");
    } else {
        console.log("add");
    
        items.push(currentItemText);
        
    }
}


async function checkTitleExists() {
    let ok = true;
    const data = await $.getJSON("note/title_note_exists/" + id + "/" + title.val());
    if (data) {
        errTitle.append("<p>⚠already exists.</p>");
        ok = false;
}
return ok;
}
$(function() {
    items = $("ul");
    title = $("#title");
    errTitle = $("#titleError");
    description = $("#description");
    errDescription = $("#errDescription");
    btnSave = $("#saveButton");
    errItem = $("#errItem");
   
    itemContent = $("li input");
    console.log(items);
    console.log(itemContent);
    itemContent.bind("input", checkUnicityItem);
    title.bind("input", checkTitle);
    title.bind("input", checkTitleExists);
    description.bind("input", checkTextContent);
    $("input[name='content[]']").bind("input", checkItemUniqueness);
    $("input:text:first").focus();
});