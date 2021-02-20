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

                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary" @click="remove_row(index)">Remove Row</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-secondary" @click="add_column()">Add Column</button>
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
                let new_row_arr = {};

                this.columns.forEach(function(item) {
                    new_row_arr[item.key] = ''
                });

                this.data.push(new_row_arr);
            },

            remove_row(row_index) {
                // remove the given row
                this.data.splice(row_index, 1);
            },

            add_column() {
                let new_column_index = Object.keys(this.columns).length - 2;

                this.columns.push({
                    key : 'new_column_' + new_column_index
                });

                this.data.forEach(function(item) {
                    item["new_column_" + new_column_index] = "";
                });

                //this.updateColumnKey(this.columns[this.columns.length - 1], "new_column_" + new_column_index)
            },

            updateColumnKey(column, event) {
                let oldKey = column.key;

                let columnKeyExists = !!this.columns.find(column => column.key === event.target.value);

                column.key = event.target.value;

                if (columnKeyExists) {
                    column.key = event.target.value.substring(0, event.target.value.length - 1);
                    return;
                }

                this.data.forEach(
                    (row) => {
                        /*
                        "IF" statemnt was removed becouse with add and edit name of
                        new column without add data to rows - not saved edited column name (based on "data" fileds keys)
                        */
                        row[column.key] = row[oldKey];
                        delete row[oldKey];
                    }
                )
            },

            submit() {
                axios.patch('/api/csv-export', this.data)
                    .then(response => {
                        var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                        var fileLink = document.createElement('a');
                        fileLink.href = fileURL;
                        fileLink.setAttribute('download', JSON.parse(response.headers['content-disposition'])[0]['filename']);
                        document.body.appendChild(fileLink);
                        fileLink.click();
                    })
            }
        },

        watch: {
        }
    }
</script>

<style scoped>

</style>
