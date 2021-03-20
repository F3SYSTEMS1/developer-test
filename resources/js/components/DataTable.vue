<template>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th v-for="(column, index) in columns">
                <input
                    type="text"
                    class="form-control"
                    :value="column.key"
                    @blur="$emit('update-column-key', index, $event)"
                />
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(row, index) in data" :key="index">
            <td v-for="column in columns">
                <input
                    type="text"
                    class="form-control"
                    v-model="row[column.key]"
                />
            </td>
            <td>
                <button
                    type="button"
                    class="btn btn-outline-danger btn-sm"
                    title="Remove row"
                    @click="$emit('remove-row')"
                >
                    <BIconTrash/>
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

    name: 'DataTable',

    props: {
        columns: {
            type: Array,
            required: true
        },
        data: {
            type: Array,
            required: true
        }
    },

    data() {
        return {
        }
    },
}
</script>

<style scoped>

</style>
