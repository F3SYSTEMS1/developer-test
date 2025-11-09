<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2'

interface Column {
    key: string;
}

type Row = Record<string, string>;

const data = ref<Row[]>([
    {
        firstName: 'John',
        lastName: 'Doe',
        emailAddress: 'john.doe@example.com',
    },
    {
        firstName: 'John',
        lastName: 'Doe',
        emailAddress: 'john.doe@example.com',
    },
]);

let columns = ref<Column[]>([
    { key: 'firstName' },
    { key: 'lastName' },
    { key: 'emailAddress' }
]);

const addRow = () => {
    const newRow: Row = {};

    columns.value.forEach(col => {
        newRow[col.key] = "";
    });

    data.value.push(newRow);
};

const removeRow = async (rowIndex: number) => {
    const containsData = Object.values(data.value[rowIndex]).some(value => {
        return value !== null && value !== undefined && value !== "";
    });

    if (!containsData) {
        data.value.splice(rowIndex, 1)
        return;
    }

    const result = await Swal.fire({
        title: "Are you sure?",
        text: "This row contains data. Are you sure you want to delete it?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    });

    if (result.isConfirmed) {
        data.value.splice(rowIndex, 1)
    }
};

const addColumn = () => {
    const newColumnIndex = String(columns.value.length - 1);

    columns.value.push({
        key: `New ${newColumnIndex}`
    });

    data.value.forEach((row: Row) => {
        row[`New ${newColumnIndex}`] = "";
    });
};

const removeColumn = async (key: string) => {
    const columnIndex = columns.value.findIndex(row => row.key === key)

    const containsData = data.value.some(value => {
        return value[key] !== null && value[key] !== undefined && value[key] !== "";
    });

    if (!containsData) {
        data.value.forEach((row) => {
            delete row[key]
        })
        columns.value.splice(columnIndex, 1)
        return;
    }

    const result = await Swal.fire({
        title: "Are you sure?",
        text: "This column contains data. Are you sure you want to delete it?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
    });

    if (result.isConfirmed) {
        data.value.forEach((row) => {
            delete row[key]
        })
        columns.value.splice(columnIndex, 1)
    }
}

const updateColumnKey = (column: Column, event: Event) => {
    const target = event.target as HTMLInputElement;
    const newKey = target.value;
    const oldKey = column.key;

    const columnKeyExists = !!columns.value.find((item) => item.key === newKey);

    column.key = newKey;

    if (columnKeyExists) {
        Swal.fire({
            title: "Duplicate Column Name",
            icon: "error",
            confirmButtonColor: "#3085d6"
        });
        column.key = oldKey
        return;
    }

    data.value.forEach((row) => {
        if (row[oldKey]) {
            row[column.key] = row[oldKey];
            delete row[oldKey];
        }
    });
};

const submit = async () => {
    try {
        const response = await axios.patch('/api/csv-export', data.value, {
            responseType: 'text',
        });

        if (response.status !== 200) {
            await Swal.fire({
                title: "Invalid Data",
                icon: "error",
                confirmButtonColor: "#3085d6"
            });
            console.error(response.statusText);
            return;
        }

        const csv = response.data;

        const blob = new Blob(["\uFEFF" + csv], {
            type: "text/csv;charset=utf-8",
        });

        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = "export.csv";
        document.body.appendChild(a);
        a.click();
        a.remove();

        URL.revokeObjectURL(url);
    } catch (err) {
        console.error("CSV export failed:", err);
    }
};
</script>

<template>
    <section class="mx-auto max-w-4xl space-y-6 px-4">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Table to CSV Generator</h2>
            <p class="text-sm text-slate-600">
                Adjust column keys and row values before exporting.
            </p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-sm font-medium text-slate-700">Editable Table</h3>
            </div>

            <div class="px-6 py-6">
                <div class="border border-gray-200 rounded-lg overflow-scroll">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th
                                    v-for="column in columns"
                                    :key="column.key"
                                    class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 min-w-42"
                                >
                                    <div class="relative">
                                        <input
                                            type="text"
                                            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                            :value="column.key"
                                            @change="updateColumnKey(column, $event)"
                                        />
                                        <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-white">
                                            <i
                                                class="pi pi-times cursor-pointer"
                                                style="font-size: 1rem; color: #555555"
                                                @click="removeColumn(column.key)"
                                            ></i>
                                        </span>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold tracking-wide text-slate-500">
                                    <button
                                        type="button"
                                        class="flex items-center justify-center rounded-md border border-slate-300 px-2 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2 cursor-pointer"
                                        @click="addColumn"
                                    >
                                        <i class="pi pi-plus" style="font-size: 1rem; color: black;"></i>
                                    </button>
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold tracking-wide text-slate-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr
                                v-for="(row, rowIndex) in data"
                                :key="rowIndex"
                                class="bg-white text-sm text-slate-700"
                            >
                                <td
                                    v-for="column in columns"
                                    :key="column.key"
                                    class="px-4 py-3"
                                >
                                    <input
                                        v-model="row[column.key]"
                                        type="text"
                                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    />
                                </td>
                                <td>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center">
                                        <i
                                            class="pi pi-times cursor-pointer"
                                            style="font-size: 1.5rem; color: darkred;"
                                            @click="removeRow(rowIndex)"
                                        ></i>
                                    </div>
                                </td>
                            </tr>
                            <tr class="bg-gray-200 text-sm text-slate-700">
                                <td
                                    v-for="column in columns"
                                    :key="column.key"
                                    class="px-4 py-3"
                                >
                                    <input
                                        type="text"
                                        disabled
                                        class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 cursor-not-allowed"
                                    />
                                </td>
                                <td>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center">
                                        <i
                                            class="pi pi-plus cursor-pointer"
                                            style="font-size: 1.5rem; color: black;"
                                            @click="addRow"
                                        ></i>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                <button
                    type="button"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:ring-offset-2 cursor-pointer"
                    @click="submit()"
                >
                    Export
                </button>
            </div>
        </div>
    </section>
</template>
