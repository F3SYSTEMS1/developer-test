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
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in data">
                                <td v-for="column in columns">
                                    <input type="text" class="form-control" v-model="row[column.key]"/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" title="Delete" @click="removeRow(row)">x</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button
                            type="button"
                            title="Maximum 10 columns"
                            class="btn btn-secondary"
                            @click="addColumn()"
                            :disabled="isAddingColumnDisabled">
                            Add Column
                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="addRow()">
                            Add Row
                        </button>

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
                let newRow = {};
                this.columns.forEach(item => newRow[item.key] = '');

                this.data.push(newRow);
            },

            addColumn() {
                if (this.isAddingColumnDisabled) {
                    return;
                }

                let newRowName = `Column(${this.columns.length})`;
                this.columns.push({
                    key: newRowName
                });

                this.data.forEach(item => {item[newRowName] = ''})
            },

            updateColumnKey(column, event) {
                const oldKey = column.key;
                const newKey = event.target.value;

                const columnKeyExists = this.columns.find(column => column.key === newKey);
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

            removeRow(row) {
                if (window.confirm('Delete this item?')) {
                    this.data = this.data.filter(item => item !== row);
                }
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
                        alert('Validation is failed');
                      } else {
                        alert('Something went wrong please try again later');
                      }
                    });
            }
        },
        computed: {
            isAddingColumnDisabled: function() {
                return this.columns.length >= 10;
            },
        },
    }
</script>

<style scoped>

</style>