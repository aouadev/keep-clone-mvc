
let title, errTitle, item_content, description, errDescription;
    
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
    ok = checkTitleExists() && ok;
    ok = checkTextContent() && ok;
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


async function checkTitleExists() {

let ok = true;
const data = await $.getJSON("note/title_note_exists/" + 0 + "/" + title.val());
if (data) {
    errTitle.append("<p>⚠already exists.</p>");
    ok = false;
}
return ok;
}
$(function() {
title = $("#title");
errTitle = $("#titleError");
item_content = $("#content");
description = $("#description");
errDescription = $("#errDescription");
btnSave = $("#saveButton");
title.bind("input", checkTitle);
title.bind("input", checkTitleExists);
description.bind("input", checkTextContent);
$("input:text:first").focus();
});