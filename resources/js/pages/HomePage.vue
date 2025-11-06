<template>
    <section class="space-y-8">
        <header class="space-y-2">
            <h2 class="text-3xl font-semibold text-slate-900">fu3e Developer Test</h2>
            <p class="text-sm text-slate-600">
                Welcome to the fu3e developer test. Follow the instructions below to complete the
                exercise.
            </p>
        </header>

        <article class="space-y-6">
            <section class="space-y-2">
                <h3 class="text-xl font-semibold text-slate-800">Introduction</h3>
                <p class="text-sm text-slate-600">
                    This is a small test based around a simple application where a user can interact
                    with a frontend Vue based table to generate CSV data. You can add rows & columns
                    and then export the table into a CSV file using a Laravel API end-point. The
                    application is incomplete and you'll need to complete the following tasks and fix
                    any bugs we have introduced.
                </p>
                <ul class="list-disc space-y-2 pl-6 text-sm text-slate-600">
                    <li>Add the ability to add a new column to the table</li>
                    <li>Add the ability to add a new row to the table</li>
                    <li>Add the ability to remove a row from the table</li>
                    <li>Add the PHP logic in the CsvExport class to convert the user input into a CSV</li>
                </ul>
            </section>

            <section class="space-y-2">
                <h3 class="text-xl font-semibold text-slate-800">What We're Looking For</h3>
                <ul class="list-disc space-y-2 pl-6 text-sm text-slate-600">
                    <li>The use of test suites</li>
                    <li>Good user experience</li>
                    <li>Meeting common coding standards in the PHP &amp; VueJs code</li>
                </ul>
            </section>

            <section class="space-y-2">
                <h3 class="text-xl font-semibold text-slate-800">Contact</h3>
                <p class="text-sm text-slate-600">
                    If you have any questions regarding the test, please email us with your questions.
                </p>
            </section>
        </article>
    </section>
</template>
