/**
 * Validates the entries in the registerForm.
 * 
 * The method checks that the user has enteed a forename, username and password in the
 * registerForm and returns an alert if any of the values are empty.
 * 
 * @returns {boolean} If the validation was successul or not.
 */
function validateRegisterForm() {
    // Get the forename, username and password from the form
    var forename = document.forms["registerForm"]["forename"].value;
    var username = document.forms["registerForm"]["username"].value;
    var password = document.forms["registerForm"]["password"].value;
    // Check if the forename, username or password are empty
    if (username == "" || forename == "" || password == "") {
        // If one of them is empty then send an alert and return the validtion was false
        alert("All values except surname must have values.")
        return false;
    }
    else {
        // Else return that the validation check has been completed successfully
        return true;
    }
}