<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expense Tracker</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold">Expense Tracker</h1>

                <nav class="space-x-4">
                    <a href="{{ route('expenses.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">View Expenses</a>
                    <a href="{{ route('groups.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">View Groups</a>
                </nav>
            </div>
        </header>

        <main class="flex-1 max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Track Your Expenses with Ease</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">Simple, intuitive, and powerful expense tracking to help you manage your finances better.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4">Track Expenses</h3>
                    <p class="text-gray-600 dark:text-gray-400">Record and categorize your expenses easily. Keep track of every penny spent.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4">Organize Groups</h3>
                    <p class="text-gray-600 dark:text-gray-400">Create expense groups to better organize and analyze your spending patterns.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold mb-4">View Reports</h3>
                    <p class="text-gray-600 dark:text-gray-400">Get insights into your spending with detailed reports and analytics.</p>
                </div>
            </div>


        </main>

        <footer class="bg-white dark:bg-gray-800 shadow mt-auto">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center text-gray-600 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} Expense Tracker. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>

</html>