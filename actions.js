let host = sessionData.host;
let port = sessionData.port;
let db = sessionData.dbName;
let table = sessionData.table;
let user = sessionData.userName;
let pass = sessionData.passWord;

function clickRow(key, id) {
    let rowData = "";
    
    const bodyData = `key=${key}&id=${id}&host=${host}&port=${port}&db=${db}&table=${table}&user=${user}&pass=${pass}`;

    fetch('http://localhost/dbviewer/server/server.php/getone', {
        method: 'POST',
        headers: {
            'Content-Type': 'text/plain'
        },
        body: bodyData
    })
    .then(response => response.text())
    .then(data => {
        rowData = data;
        console.log(rowData);
    })
    .catch(error => {
        console.error('Error:', error);
    });

    return rowData.toString();
}