let host = sessionData.host;
let port = sessionData.port;
let db = sessionData.dbName;
let table = sessionData.table;
let user = sessionData.userName;
let pass = sessionData.passWord;

async function clickRow(key, id) {    
    const bodyData = `key=${key}&id=${id}&host=${host}&port=${port}&db=${db}&table=${table}&user=${user}&pass=${pass}`;

    try {
        // URL to Backend
        const url = `${window.location.origin}${window.location.pathname.replace(/\/[^/]*$/, '')}/server/server.php/getone`;
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'text/plain'
            },
            body: bodyData
        });

        const rowData = await response.json();

        if (!rowData || Object.keys(rowData).length === 0) {
            console.error('Error: No data received from server');
        } 
        else if (rowData.error) {
            console.error(rowData.error);
        } 
        else {
            console.log(JSON.stringify(rowData, null, 2));
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
        // URL to Backend
        const url = `${window.location.origin}${window.location.pathname.replace(/\/[^/]*$/, '')}/server/server.php/getall?${queryParams}`;
        const response = await fetch(url, {
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