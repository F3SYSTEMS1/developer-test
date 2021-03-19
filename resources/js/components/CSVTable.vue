<template>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th v-for="column in columns">
                <input type="text"
                       :class="[column.has_error ? 'is-invalid' : '', 'form-control']"
                       :value="column.key"
                       @blur="$emit('update-column-key', column, $event)"
                />
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(row, index) in data">
            <td v-for="column in columns">
                <input type="text" class="form-control" v-model="row[column.key]"/>
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm"
                        title="Remove row"
                        @click="$emit('remove-row')">
                    <BIconTrash></BIconTrash>
                </button>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
import { BIconTrash } from 'bootstrap-vue';
export default {
    components: {
        BIconTrash,
    },

    name: 'CSVTable',

    props: ['columns', 'data'],

    data() {
        return {
        }
    },

    methods: {

    },

    watch: {

    }
}
</script>

<style scoped>

</style>
