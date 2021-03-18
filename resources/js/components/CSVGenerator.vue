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
                                    &nbsp;
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" @click="removeRow(index)">&times;</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-secondary" @click="addColumn()">Add Column</button>
                        <button type="button" class="btn btn-secondary" @click="addRow()">Add Row</button>
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
            addRow() {
                const row = {};
                this.columns.forEach(column => {
                    row[column.key] = '';
                })
                this.data.push(row);
            },

            removeRow(index) {
                if (confirm('Are you sure?')) {
                    this.data.splice(index, 1);
                }
            },

            addColumn() {
                const key = 'column ' + (Object.keys(this.columns).length + 1);
                this.columns.push({key})
                this.data.forEach(item => {
                    item[key] = '';
                });

            },

            updateColumnKey(column, event) {
                let oldKey = column.key;

                let columnKeyExists = !!this.columns.find(column => column.key === event.target.value);

                column.key = event.target.value;

                if (columnKeyExists) {
                    column.key = event.target.value.substring(0, event.target.value.length - 1);
                    return;
                }
                let newData = [];
                this.data.forEach(
                    (row) => {
                        //There was a bug here: https://youtu.be/rKtXNaDlhmE
                        //
                        //This approach increases the сomputational complexity (since we have nested loops),
                        // but it shouldn't be an issue for performance (assumed, number of columns is not a big number
                        // (simply, place on the screen will end faster than it will bring any performance issues);
                        // it was tested on a grid of 20x20 and didn't have any impact on the performance;
                        // but still, there's a place to improve)
                        let newRow = {};
                        this.columns.forEach(col => {
                            newRow[col.key] = (col.key === column.key) ? row[oldKey] : row[col.key];
                        });
                        newData.push(newRow);
                    }
                )
                this.data = newData;
            },

            submit() {
                return axios
                    .patch('/api/csv-export', this.data)
                    .then(response => {
                        const link = document.createElement('a');
                        link.setAttribute('href', window.URL.createObjectURL(new Blob([response.data])));
                        link.setAttribute('download', 'file.csv');
                        document.body.appendChild(link);
                        link.click();
                    })
                    .catch(err => {
                        alert('Something went wrong');
                        console.error(err);
                    })
            }
        },

        watch: {
        }
    }
</script>

<style scoped>

</style>
