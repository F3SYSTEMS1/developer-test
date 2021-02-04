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
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-secondary" v-on:click="add_column()">Add Column</button>
                        <button type="button" class="btn btn-secondary" v-on:click="add_row()">Add Row</button>
                        <button type="button" class="btn btn-secondary" v-on:click="delete_column()">Delete Column</button>
                        <button type="button" class="btn btn-secondary" v-on:click="delete_row()">Delete Row</button>
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
    import {exportCsvFile} from "../api";

    export default {
        name: "CSVGenerator",

        data() {
            return {
                data: [
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com',
                    },
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com'
                    },

                ],
                columns: [
                    {key: 'first_name',},
                    {key: 'last_name', },
                    {key: 'emailAddress',},

                ]
            }
        },

        methods: {
            add_row() {
                let row = {}
                this.columns.map((column) => {
                    row[column.key] = ''
                })

                this.data.push(row)
            },

            remove_row(row_index) {
                if(this.data.length <= 1){
                    return false
                }
                this.data.splice(row_index, 1)
            },

            delete_row() {
                if(this.data.length <= 1){
                    return false
                }
                this.data.splice(this.data.length - 1, 1)
            },
            getUniqueColumnName(columnIndex){
                let propertyName = 'newColumn' + '-' + columnIndex

                if(this.columnKeyExists(propertyName)){
                    return this.getUniqueColumnName(++columnIndex)
                }
                return propertyName;
            },
            add_column() {
                let columnName = this.getUniqueColumnName(this.columns.length)

                this.columns.push({key: columnName})

                this.data.map((row, index) => {
                    row[columnName] = ''
                })
            },
            delete_column(){
                if(this.columns.length <= 1){
                    return false
                }
                const columnToDelete = this.columns[this.columns.length - 1]
                this.data.map((row, index) => {
                    delete row[columnToDelete.key]
                })
                this.columns.splice(this.columns.length - 1, 1)
            },

            columnKeyExists(columnKey) {
                return !!this.columns.find(column => column.key === columnKey);
            },

            updateColumnKey(column, event) {
                let oldKey = column.key;
                let columnKeyExists = this.columnKeyExists(event.target.value)
                column.key = event.target.value;
                if (columnKeyExists) {
                    column.key = event.target.value.substring(0, event.target.value.length - 1);
                    return;
                }
                this.data.forEach(
                    (row) => {
                        if (row[oldKey] || row[oldKey] !== undefined) {
                            row[column.key] = row[oldKey];
                            delete row[oldKey];
                        }
                    }
                )
            },

            submit() {
                return exportCsvFile(this.data)
            }
        },

        watch: {
        }
    }
</script>

<style scoped>

</style>
