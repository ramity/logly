// Capturing runtime script errors
window.onerror = function(message, source, lineno, colno, error) {

    console.log("Error message: " + message);  // Error message as a string
    console.log("Source file: " + source);     // The URL of the script where the error occurred
    console.log("Line number: " + lineno);     // Line number where the error happened
    console.log("Column number: " + colno);    // Column number of the error
    console.log("Error object: ", error);      // The Error object, which may contain stack trace

    var event = {
        "message": message,
        "source": source,
        "lineno": lineno,
        "colno": colno,
        "error": error,
        "error_type": "runtime"
    };
    feed(event);

    // return true; // Prevents the default error handling
};

// Capture resource errors
window.addEventListener("error", function(event) {
    console.log("Resource failed to load: ", event.target.src || event.target.href);

    var event = {
        "error_type": "resource",
        "target": event.target.src || event.target.href
    };
    feed(event);

}, true);

// Capture unhanled promise errors
window.onunhandledrejection = function(event) {
    console.log("Unhandled Promise Rejection: ", event.reason);

    var event = {
        "error_type": "promise",
        "reason": event.reason
    };
    feed(event);
};

// Logic to send errors to central server
function feed(event)
{
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'https://192.168.1.4:8000/ingest', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            console.log('Success:', JSON.parse(xhr.responseText));
        } else {
            console.error('Error:', xhr.status, xhr.statusText);
        }
    };
    xhr.onerror = function() {
        console.error('Network error');
    };
    xhr.send(JSON.stringify(event))
}
