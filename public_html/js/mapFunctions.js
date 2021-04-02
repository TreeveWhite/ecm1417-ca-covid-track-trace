/**
 * Gets the x and y co-ordiantes of where the user click on the map.
 * 
 * This method gets the x and y co-ordinates then stored them in the value
 * of the given xdestiationid and ydestinationid elements in the html page.
 * 
 * @param {Object} event The onclick event.
 * @param {String} xdestinationid The id of the element to store the x coordinant in.
 * @param {String} ydestinationid The id of the element to store the y coordinant in.
 */
function getCoordinants(event, xdestinationid, ydestinationid) {
    // Gets the x and y values where the user has clicked.
    var x = event.offsetX;
    var y = event.offsetY-13;
    // Gets the marker image
    var marker = document.getElementById("markerPoint");
    // Sets the position of the marker to where the user has clicked
    marker.style.setProperty("top", y + "px");
    marker.style.setProperty("left", x + "px");
    // Sets the value of the hidden inputs in addVisitForm to the x and y values
    document.getElementById(xdestinationid).value = x;
    document.getElementById(ydestinationid).value = y;
}

/**
 * Displays an alert box with details about a visit.
 * 
 * This method is called when a visit on the homepage is clicked and the user wishes to know
 * more details about the infection and visit. These extra details are then displayed in the
 * alert box.
 * 
 * @param {String} infectionDate The date the infection was reported.
 * @param {String} infectionTime The time the infection was reported.
 * @param {String} visitDate The date of the clicked visit.
 * @param {String} visitTime The time of the clicked visit.
 * @param {String} visitDuration Te duration of the clicked visit.
 */
function displayInfectionDetails(infectionDate, infectionTime, visitDate, visitTime, visitDuration) {
    if (infectionDate == "Unknown")  {
        extraDetails = "\n(This visit is taken from the web service so infection report details are unknown).\n";
    }
    else {
        extraDetails = "\n(This visit is taken from the database so infection report details are known).\n";
    }
    alert("Visit Infomation\nDate: " + visitDate + " Time: " + visitTime + " Duration: " + visitDuration + " minutes\n"+extraDetails+"\nInfection Report Infomation\nDate: " + infectionDate + " Time: " + infectionTime);
}
