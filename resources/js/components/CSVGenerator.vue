<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">Table to CSV Generator</div>

                    <div class="card-body">
                        <table class="table table-bordered table-responsive-md">
                            <thead>
                            <tr>
                                <th v-for="column in columns" :key="column.key">
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
                            <tr v-for="(row, index) in  data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                                <td class="text-center">
                                    <b-button variant="outline-secondary" @click="removeRow(index)">
                                        <b-icon class="h2 pointer-event align-bottom m-1" icon="trash-fill"
                                                variant="danger"
                                                aria-hidden="true"></b-icon>
                                    </b-button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                <b-input-group>
                                    <b-input-group-prepend>
                                        <b-button variant="secondary"
                                                  :disabled="!areNewColumnsValid" @click="addColumns(quantity.columns)">
                                            Add Columns
                                        </b-button>
                                    </b-input-group-prepend>

                                    <b-form-input :state="validateState('columns')"
                                                  @change="changeColumnQuantity"
                                                  v-model="$v.quantity.columns.$model" type="number" min="1" max="5"
                                                  step="1"></b-form-input>
                                    <b-form-invalid-feedback v-if="!$v.quantity.columns.required">Field is required
                                    </b-form-invalid-feedback>
                                    <b-form-invalid-feedback v-if="!$v.quantity.columns.maxValue">The column quantity
                                        must be
                                        5 or less
                                    </b-form-invalid-feedback>
                                </b-input-group>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3 col-xl-2 mt-3 mt-lg-0">
                                <b-dropdown :variant="!areNewColumnsValid ? 'danger' : 'secondary'" text="Column Names"
                                            ref="dropdown">
                                    <b-dropdown-form>
                                        <b-form-group v-for="(newColumn, index) in $v.newColumns.$each.$iter"
                                                      :key=index>
                                            <b-form-input
                                                :class="{ 'is-invalid': !newColumn.key.required }"
                                                size="sm"
                                                v-model="newColumn.key.$model"
                                                @input.native="changeNewColumnValue(newColumn, index, $event)"
                                            ></b-form-input>
                                            <b-form-invalid-feedback v-if="!newColumn.key.required">Name is required
                                            </b-form-invalid-feedback>
                                        </b-form-group>
                                    </b-dropdown-form>
                                </b-dropdown>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3 col-xl-3 mt-3 mt-md-0 offset-lg-3 offset-xl-4">
                                <b-input-group>
                                    <b-input-group-prepend>
                                        <b-button variant="secondary"
                                                  :disabled="!$v.quantity.rows.maxValue || !$v.quantity.rows.required"
                                                  @click="addRows(quantity.rows)">Add Rows
                                        </b-button>
                                    </b-input-group-prepend>

                                    <b-form-input v-model="$v.quantity.rows.$model"
                                                  :state="validateState('rows')" type="number"
                                                  min="1" max="10" step="1"></b-form-input>
                                    <b-form-invalid-feedback v-if="!$v.quantity.rows.required">Field is required
                                    </b-form-invalid-feedback>
                                    <b-form-invalid-feedback v-if="!$v.quantity.rows.maxValue">The row quantity must be
                                        10 or less
                                    </b-form-invalid-feedback>
                                </b-input-group>
                            </div>
                        </div>
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
import {validationMixin} from "vuelidate";
import {required, maxValue} from 'vuelidate/lib/validators'

export default {
    mixins: [validationMixin],
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
                {key: 'first_name'},
                {key: 'last_name'},
                {key: 'emailAddress'},
            ],
            newColumns: [
                {key: ''},
            ],
            quantity: {
                columns: 1,
                rows: 1
            },
        }
    },
    validations: {
        quantity: {
            rows: {
                required,
                maxValue: maxValue(10)
            },
            columns: {
                required,
                maxValue: maxValue(5)
            },
        },
        newColumns: {
            $each: {
                key: {required}
            }
        }
    },
    computed: {
        columnsLength() {
            return this.columns.length
        },
        areNewColumnsValid() {
            return this.newColumns.every(item => item.key);
        }
    },
    methods: {
        validateState(name) {
            const {$dirty, $error} = this.$v.quantity[name];
            return $dirty ? !$error : null;
        },
        changeColumnQuantity(quantity) {
            if (quantity > 0 && quantity <= 5) {
                this.newColumns = [];
                for (let i = 0; i < quantity; i++) {
                    this.newColumns.push({
                        key: '',
                    });
                }
            }
        },
        changeNewColumnValue(column, index, event) {
            let newKey = event.target.value;
            if (newKey && this.isDuplicate(this.newColumns)) {
                event.target.value = newKey.slice(0, -1);
                return;
            }

            let columnKeyExists = this.columns.find(column => column.key === newKey);
            if (columnKeyExists) {
                event.target.value = newKey.slice(0, -1);
                return;
            }

            this.newColumns[index].key = event.target.value;
        },
        addRows(quantityRows) {
            // Add new row to data with column key
            for (let i = 0; i < parseInt(quantityRows); i++) {
                let newRow = {};
                this.columns.forEach(item => newRow[item.key] = '');
                this.data.push(newRow);
            }
        },
        removeRow(rowIndex) {
            // remove the given row
            this.boxTwo = ''
            this.$bvModal.msgBoxConfirm('Please confirm that you want to delete this row.', {
                title: 'Please Confirm',
                size: 'sm',
                buttonSize: 'sm',
                okVariant: 'danger',
                okTitle: 'YES',
                cancelTitle: 'NO',
                footerClass: 'p-2',
                hideHeaderClose: false,
                centered: true
            })
                .then(value => {
                    if (value) {
                        this.data.splice(rowIndex, 1);
                    }
                })
                .catch(err => {
                    // An error occurred
                })
        },
        addColumns() {
            const newColumns = this.newColumns.map(a => ({...a}));
            this.columns.push(...newColumns);
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
            return axios.patch('/api/csv-export', {'data': this.data},
                {responseType: 'blob'})
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', this.generateFileName());
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(function (error) {
                    if (error.response.status === 422) {
                        this.$bvModal.msgBoxOk('Invalid data');
                    } else {
                        this.$bvModal.msgBoxOk(error.response.data.message || 'Undefined error');
                    }
                });
        },
        isDuplicate(columns) {
            let arrayOfValues = columns.filter(function (item) {
                if (item.key) {
                    return item.key
                }
            });
            return arrayOfValues.some(function (item, idx) {
                return arrayOfValues.indexOf(item) != idx
            });
        },
        resetNewColumns() {
            this.quantity.columns = 1;
            this.newColumns.splice(1)
            this.newColumns[0].key = '';
        },
        generateFileName() {
            const date = new Date();
            const year = date.getFullYear();
            const month = `${date.getMonth() + 1}`.padStart(2, '0');
            const day = `${date.getDate()}`.padStart(2, '0');
            return `exported_${year}-${month}-${day}.csv`
        }
    },
    watch: {
        columnsLength(current, prev) {
            let newColumnsNames = this.newColumns.map(function (item) {
                return item.key;
            });
            this.data = this.data.map(item => {
                return Object.assign(item, ...Object.entries({...newColumnsNames}).map(([a, b]) => ({[b]: ''})))
            });
            this.resetNewColumns();
        },
    }
}
</script>

<style scoped>
</style>
