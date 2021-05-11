<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">Table to CSV Generator</div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th v-for="column in columns">
                                    <input type="text"
                                           class="form-control"
                                           :value="column.key"
                                           @input="updateColumnKey(column, $event)"
                                    />
                                </th>
                                <th>
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in  data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                                <td>
                                    <i  @click="remove_row(index)" style="font-size:25px;color:red;cursor:pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                        </svg>

                                    </i>

                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-secondary"  @click="add_column()">Add Column</button>
                        <button type="button" class="btn btn-secondary" @click="add_row()">Add Row</button>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="button" @click="submit()">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "CSVGenerator",
        data() {
            return {
                data: [
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com'
                    },
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com'
                    },

                ],
                columns: [
                    {key: 'first_name'},
                    {key: 'last_name'},
                    {key: 'emailAddress'},

                ]
            }
        },

        methods: {
            add_row() {
                // Add new row to data with column key
                let newRecord = {};
                this.columns.forEach(item => newRecord[item.key] = '');
                this.data.push(newRecord);
            },
            remove_row(row_index) {
                // remove the given row

                this.$bvModal.msgBoxConfirm('Are you sure you want to delete?')
                    .then(value => {
                       if(value)
                       {
                           this.data.splice(row_index,1);
                       }
                    })
                    .catch(err => {
                        // An error occurred
                    })

            },

            add_column() {
                let newRow = `newColumn`;
                this.columns.push({
                    key: newRow
                });
                this.data.forEach(item => {item[newRow] = ''})
            },

            updateColumnKey(column, event) {
                let oldKey = column.key;
                let newKey = event.target.value;

                let columnKeyExists = this.columns.find(column => column.key === newKey);

                if (columnKeyExists) {
                    event.target.value = oldKey;
                    return;
                }
                column.key = newKey;
                this.data.forEach(
                    (row) => {
                        if (row[oldKey]) {
                            row[column.key] = row[oldKey];
                            delete row[oldKey];
                        }
                    }
                )
            },

            submit() {
                return axios.patch('/api/csv-export', {'data': this.data}, {responseType: 'blob'})
                    .then(response => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'data.csv');
                        document.body.appendChild(link);
                        link.click();
                    })
                    .catch(function (error) {
                        if (error.response.status === 422) {
                            this.$bvModal.msgBoxOk('Data is not in correct format');
                        } else {
                            this.$bvModal.msgBoxOk('Error !!!');
                        }
                    });
            },

        },

        watch: {
            columns: function (columns) {
                const lastAddedColumnKey = columns[columns.length - 1].key;
                this.data = this.data.map(item => {
                    if (!item.hasOwnProperty(lastAddedColumnKey)) {
                        item[lastAddedColumnKey] = null;
                    }
                    return item;
                });
            }
        }
    }
</script>

<style scoped>

</style>
