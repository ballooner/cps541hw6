// Get the #databaseOperation selection drop-down
let dropDown = document.querySelector("#databaseOperation");
// Get the PHP form
let phpForm = document.querySelector("#phpForm");

// Insert required form elements when the corresponding drop-down option is selected
dropDown.addEventListener("change", (e) => 
{
    // Clear the children from the PHP form
    phpForm.textContent = "";

    // Get the correct template and copy it into the form
    let formTemplateClone = document.getElementById(dropDown.value).content.cloneNode(true);
    phpForm.appendChild(formTemplateClone);
});
