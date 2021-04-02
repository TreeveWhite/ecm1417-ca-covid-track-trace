/**
 * Deletes a visit from the database
 * 
 * This fuction deletes a specific visit from the database specified by the given
 * parameters date, time, duration and the (x, y) co-odinates of the visit location.
 * 
 * @param {Int16Array} rowId The id of the row to be deleted.
 * @param {string} date The date f the visit to be deleted.
 * @param {string} time The time of the visit to be deleted.
 * @param {string} duration The duration of the visit to be deleted.
 * @param {Int16Array} x The x co-ordiante of the visit to be deleted.
 * @param {Int16Array} y Teh y co-ordinate of the visit to be deleted.
 */
function deleteVisit(rowId, date, time, duration, x, y) {
    // Creates a new XML Request
    var xhttp = new XMLHttpRequest();
    // Defines a functio which is called when the ready state changes
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            table = document.getElementById("overviewTable");
            table.deleteRow(rowId);
        }
    }
    // Creates a new HTTP request GET deleteVisit.php
    xhttp.open("GET", "deletevisit.php?date="+date+"&time="+time+"&duration="+duration+"&x="+x+"&y="+y, true);
    // Sends the HTTP request
    xhttp.send();
}