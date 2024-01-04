
// const eventSource = new EventSource('http://localhost/api/status');
// const eventSource = new EventSource('http://localhost/api/status');

// eventSource.onmessage = function(event) {
//     console.log('Received data:', event.data);
// };

// eventSource.onerror = function(error) {
//     console.error('EventSource failed:', error);
//     eventSource.close();
// };
const tbody = document.getElementById('gs-table')
var tr = tbody.querySelector('tr')
const reloadButton = document.getElementById('reload-button')

const modal = document.getElementById('modal')
const cancelButton = document.getElementById('calcelButton')
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
    getGs()
})

var deletePodName = ""

getGs()


reloadButton.addEventListener('click',(event) => {
    rimg = reloadButton.querySelector('img')
    rimg.className = 'w-5 h-5 animate-spin'
    getGs()
    rimg.className = 'w-5 h-5'

})

function getGs(){

    var request = new XMLHttpRequest();

    request.open('GET','http://localhost/api/status')
    request.responseType = 'json';

    request.onload = () => {
        console.log(request.response)
        var data = request.response
        if(data == null ){
            console.log('error: data is null')
        }
        while( tbody.firstChild ){
            tbody.removeChild( tbody.firstChild );
        }
        console.log(data.length == 0)
        if(data.length == 0){
            console.log("ifjeioawjfejwaoi")
            const tr = document.createElement('tr')
            const td = document.createElement('td')
            td.className = "text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200"
            td.innerHTML = "サーバがありません。サーバを作成してください。"
            td.colSpan = 5
            tr.appendChild(td)
            tbody.appendChild(tr)
        }
    
        data.forEach((d) => {
            var newRow = tr.cloneNode(true)
            newRow.cells[0].textContent = d.name
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
    }
    request.send()
}

function formatDate(dateString) {
    console.log(dateString)
    var date = new Date(dateString);

    var year = date.getFullYear();
    var month = date.getMonth() + 1; // 月は0から始まるので1を加える
    var day = date.getDate();
    console.log( date.getDate()    )

    return year + '年' + month + '月' + day + '日';
}






// request.onload = () => {
//     var data = this.request.response

//     data.forEach((d) => {
//         const tr = document.createElement('tr')
//         // var value = Object.values(d)
//         colum.forEach((c) => {
//             const td = document.createElement('td')
//             value = d[c]
//             if(c == 'created_at'){
//                 value  = formatDate(d[c])
//             }
//             td.innerHTML = value
        
//             console.log(d[c])
//             tr.appendChild(td)
//         })
//         tbody.appendChild(tr)
//     })
    
//     console.log(data)
// }