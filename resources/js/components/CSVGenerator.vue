<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">Table to CSV Generator</div>

                    <div class="card-body">
                        <CSVTable
                            :columns="columns"
                            :data="data"
                            @remove-row="removeRow"
                            @update-column-key="updateColumnKey"
                        ></CSVTable>

                        <button type="button" class="btn btn-secondary" @click="addColumn">Add Column</button>
                        <button type="button" class="btn btn-secondary" @click="addRow">Add Row</button>
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
    import { BIconTrash } from 'bootstrap-vue';
    import CSVTable from './CSVTable';
    export default {
        components: {
            BIconTrash, CSVTable
        },

        name: 'CSVGenerator',

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
                    {key: 'first_name', has_error: false},
                    {key: 'last_name', has_error: false},
                    {key: 'emailAddress', has_error: false},
                ]
            }
        },

        methods: {
            addRow() {
                let newItem = {
                    first_name: 'John',
                    last_name: 'Doe',
                    emailAddress: 'john.doe@example.com'
                };

                for (let column of this.columns) {
                    if (!newItem[column.key]) {
                        newItem[column.key] = null;
                    }
                }

                this.data.push(newItem);
                // Add new row to data with column keys
            },

            removeRow(rowIndex) {
                this.data.splice(rowIndex, 1);
                // remove the given row
            },

            addColumn() {
                const columnKey = 'column_' + this.columns.length;
                this.columns.push({
                    key: columnKey
                });
            },

            updateColumnKey(column, event) {
                const oldKey = column.key;
                const wasChanged = column.key !== event.target.value;

                if (!wasChanged) {
                    column.has_error = false;
                    return;
                }

                const columnKeyExists = !!this.columns.find(column => column.key === event.target.value);

                if (columnKeyExists) {
                    this.$fire({
                        title: 'Error',
                        text: 'Column name already in use!',
                        type: 'warning',
                        timer: 2000
                    });
                    column.has_error = true;
                    return;
                }

                column.key = event.target.value;
                column.has_error = false;

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
                return axios.patch('/api/csv-export', this.data);
            }
        },

        watch: {
            columns: function (columns) {
                const lastAddedColumnKey = columns[columns.length - 1].key;

                this.data = this.data.map(item => {
                    item[lastAddedColumnKey] = null;

                    return item;
                });
            }
        }
    }
</script>

<style scoped>

</style>
