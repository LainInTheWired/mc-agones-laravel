const tbody = document.getElementById('gs-table')
var tr = tbody.querySelector('tr')
const reloadButton = document.getElementById('reload-button')

const logbar = document.getElementById("logbar")
const modal = document.getElementById('modal')
const cancelButton = document.getElementById('calcelButton')
const logtext = document.getElementById("logtext")
cancelButton.addEventListener('click',() => {
    modal.style.display = "none"
    console.log(modal.style)
})
const deleteButton = document.getElementById('deleteButton')
deleteButton.addEventListener('click',() => {
    var request = new XMLHttpRequest();
    request.open('POST','http://localhost/api/delete')
    request.setRequestHeader('content-type','application/x-www-form-urlencoded;charaset=UTF-8')
    request.send('name=' + deletePodName)
    deletePodName = ''
    modal.style.display = "none"
})

noGsTable()

$statustreamURL = "http://localhost/api/statusstream"

getStatusstream($statustreamURL)
function formatDate(dateString) {
    console.log(dateString)
    var date = new Date(dateString);

    var year = date.getFullYear();
    var month = date.getMonth() + 1; // 月は0から始まるので1を加える
    var day = date.getDate();
    console.log( date.getDate()    )

    return year + '年' + month + '月' + day + '日';
}
function noGsTable() {
    while(tbody.firstChild ){
        tbody.removeChild( tbody.firstChild );
    }
    console.log("responce data is no data")
    const tr = document.createElement('tr')
    const td = document.createElement('td')
    td.className = "text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200"
    td.innerHTML = "サーバがありません。サーバを作成してください。"
    td.colSpan = 5
    tr.appendChild(td)
    tbody.appendChild(tr)
}

function getStatusstream($url){
    const eventSource = new EventSource('http://localhost/api/statusstream');

    eventSource.onmessage = function(event) {
        // console.log('Received data:', event.data);
        var data = JSON.parse(event.data)
        console.log(data)
        if(data == null ){
            console.log('error: data is null')
        }
        while(tbody.firstChild ){
            tbody.removeChild( tbody.firstChild );
        }
        
        console.log(data.length)
        console.log(data.length == 0)
        if(data.length == 0){
            console.log("responce data is no data")
            const tr = document.createElement('tr')
            const td = document.createElement('td')
            td.className = "text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200"
            td.innerHTML = "サーバがありません。サーバを作成してください。"
            td.colSpan = 5
            tr.appendChild(td)
            tbody.appendChild(tr)
        }
        function getLogstream (name){
            const logStreamEvent = new EventSource('http://localhost:8880/logstream?name=' + name);
            logbar.innerText = "Log:" + name
            logStreamEvent.onmessage = (event) => {
                console.log("" + event.data)
                console.log(logtext)
                if(event.data != null){
                    logtext.innerHTML += event.data + "\n";
                    logtext.scrollTop += logtext.scrollHeight;
                }
            }
            logStreamEvent.onerror = function(error) {
                console.error('get logstream EventSource failed:', error);
                logStreamEvent.close();
                setTimeout(() => getLogstream(name), 1000);

            };
        }
        data.forEach((d) => {
            console.log(d)
            var newRow = tr.cloneNode(true)
            newRow.cells[0].textContent = d.name
            newRow.cells[0].addEventListener('click',(e) => {
                console.log("click " + d.name)
                getLogstream(d.name)
                 
        
            })
            newRow.cells[1].textContent = formatDate(d.created_at)
            newRow.cells[2].textContent = d.domain
            newRow.cells[3].textContent = d.status
            newRow.cells[4].querySelector('a').addEventListener('click',(e) => {
                e.preventDefault();
                deletePodName = d.name
                modal.style.display = "block"
                console.log(modal.style)
            },false)
    
            console.log(newRow.cells[4])
            tbody.appendChild(newRow)
        })
    };
    
    eventSource.onerror = function(error) {
        console.error('get statustream EventSource failed:', error);
        eventSource.close();
        setTimeout(() => getStatusstream($statustreamURL), 1000);
    };


} 