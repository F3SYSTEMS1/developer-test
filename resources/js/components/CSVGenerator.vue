<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">Table to CSV Generator</div>

                    <div class="card-body">
                        <DataTable
                            :columns="columns"
                            :data="data"
                            @remove-row="removeRow"
                            @update-column-key="updateColumnKey"
                        />

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="addColumn"
                        >
                            Add Column
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="addRow"
                        >
                            Add Row
                        </button>
                    </div>

                    <div class="card-footer">
                        <div class="text-left">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    v-model="includeHeaders"
                                >
                                <label class="form-check-label">Include headers</label>
                            </div>
                        </div>
                        <div class="text-right">
                            <button
                                class="btn btn-primary"
                                type="button"
                                @click="submit"
                                :disabled="this.submitted"
                            >
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DataTable from './DataTable';
    export default {
        components: {
            DataTable
        },

        name: 'CSVGenerator',

        data() {
            return {
                data: [
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        email_address: 'john.doe@example.com'
                    },
                ],
                includeHeaders: true,
                submitted: false,
                columns: [
                    {key: 'first_name'},
                    {key: 'last_name'},
                    {key: 'email_address'},
                ]
            }
        },

        methods: {
            addRow() {
                let newItem = {};

                for (let column of this.columns) {
                    newItem[column.key] = null;
                }

                this.data.push(newItem);
            },

            removeRow(rowIndex) {
                this.data.splice(rowIndex, 1);
            },

            addColumn() {
                const columnKey = 'column_' + this.columns.length;
                this.columns.push({
                    key: columnKey
                });
            },

            updateColumnKey(index, event) {
                let column = this.columns[index];
                let oldKey = column.key;

                const wasChanged = column.key !== event.target.value;
                if (!wasChanged) {
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
                    column.key = oldKey;
                    Vue.set(this.columns, index, column);
                    return;
                }

                column.key = event.target.value;
                Vue.set(this.columns, index, column);

                this.data.forEach(
                    (row) => {
                        if (row[oldKey]) {
                            row[column.key] = row[oldKey];
                        }
                        delete row[oldKey];
                    }
                );
            },

            submit() {
                this.submitted = true;
                return axios.patch('/api/csv-export', {
                    data: this.data,
                    include_headers: this.includeHeaders,
                }).then(response => {
                    const fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    const fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', this.generateFilename());
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    fileLink.remove();
                }).catch(error => {
                    this.$fire({
                        text: error.response.data.message || 'Unknown error',
                        type: 'warning',
                        timer: 2000
                    });
                }).finally(() => {
                    this.submitted = false;
                });
            },

            generateFilename() {
                return 'export_' + Date.now() + '.csv';
            }
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
