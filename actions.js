function clickRow(key, id, host, port, db, table, user, pass = "") {
    let rowData = "";

    const bodyData = `key=${key}&id=${id}&host=${host}&port=${port}&db=${db}&table=${table}&user=${user}&pass=${pass}`;

    fetch('server.php/getone', {
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