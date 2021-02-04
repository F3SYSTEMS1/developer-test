export function exportCsvFile(data){
    return axios.patch('/api/csv-export', data, {responseType: 'blob'}).then((response) => {
        var fileURL = window.URL.createObjectURL(new Blob([response.data], {type: 'application/csv'}));
        var fileLink = document.createElement('a');
        fileLink.href = fileURL;
        fileLink.setAttribute('download', 'fu3e.csv');
        document.body.appendChild(fileLink);
        fileLink.click();
        document.body.removeChild(fileLink);
    });
}
