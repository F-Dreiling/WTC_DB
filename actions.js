let host = sessionData.host;
let port = sessionData.port;
let db = sessionData.dbName;
let table = sessionData.table;
let user = sessionData.userName;
let pass = sessionData.passWord;

async function clickRow(key, id) {    
    const bodyData = `key=${key}&id=${id}&host=${host}&port=${port}&db=${db}&table=${table}&user=${user}&pass=${pass}`;

    try {
        const response = await fetch('http://localhost/dbviewer/server/server.php/getone', {
            method: 'POST',
            headers: {
                'Content-Type': 'text/plain'
            },
            body: bodyData
        });

        const rowData = await response.text();

        if (rowData === "" || rowData === null) {
            console.error('Error: No data received from server');
        } 
        else if (rowData.substring(0, 5) === "Error") {
            console.error(rowData);
        } 
        else {
            console.log(rowData);
        }
    } 
    catch (error) {
        console.error('Error: ', error);
    }
}

async function printJson() {
    const queryParams = new URLSearchParams({
        host: host,
        port: port,
        db: db,
        table: table,
        user: user,
        pass: pass
    });

    try {
        const response = await fetch(`http://localhost/dbviewer/server/server.php/getjson?${queryParams}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const jsonData = await response.json();

        if (!jsonData || Object.keys(jsonData).length === 0) {
            console.error('Error: No data received from server');
        } 
        else if (jsonData.error) {
            console.error(jsonData.error);
        } 
        else {
            const blob = new Blob([JSON.stringify(jsonData, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            window.open(url);
        }
    } 
    catch (error) {
        console.error('Error: ', error);
    }
}