console.log("logstream.js")
const logtext = document.getElementById("logtext")
console.log(logtext.innerHTML)

const logStreamEvent = new EventSource('http://localhost:8880/logstream?name=a');
// const logStreamEvent = new EventSource('http://localhost/api/statusstream');
// const logStreamEvent = new EventSource('http://localhost:8880/statusstream?userid=1');

logStreamEvent.onmessage = (event) => {
    console.log("" + event.data)
    console.log(logtext)
    if(event.data != null){
        logtext.innerHTML += event.data + "\n";
        logtext.scrollTop += logtext.scrollHeight;

    }
}
logStreamEvent.onerror = function(error) {
    console.error('EventSource failed:', error);
    logStreamEvent.close();
};