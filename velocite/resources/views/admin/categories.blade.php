<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">Bike Categories</h1>
                            <p class="text-gray-600">Manage the categories for bikes on the Vélocité platform</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex">
                            <a href="{{ route('admin.bikes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                Back to Bikes
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Create Category Form -->
                        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
                            <div class="p-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">Create New Category</h3>
                            </div>
                            <div class="p-4">
                                <form action="{{ route('admin.categories.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" id="name" name="name" required
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="mb-4">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea id="description" name="description" rows="3"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Create Category
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Categories List -->
                        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200 md:col-span-2">
                            <div class="p-4 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">All Categories</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bikes</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($categories as $category)
                                            <tr id="category-{{ $category->id }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div id="category-name-{{ $category->id }}" class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                                    <div id="category-desc-{{ $category->id }}" class="text-sm text-gray-500">{{ $category->description }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $category->bikes_count }} bikes</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button type="button" onclick="showEditForm({{ $category->id }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                                    @if($category->bikes_count == 0)
                                                        <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr id="edit-form-{{ $category->id }}" class="hidden bg-gray-50">
                                                <td colspan="3" class="px-6 py-4">
                                                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-3">
                                                        @csrf
                                                        @method('PUT')
                                                        <div>
                                                            <label for="edit-name-{{ $category->id }}" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                                            <input type="text" id="edit-name-{{ $category->id }}" name="name" value="{{ $category->name }}" required
                                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                                        </div>
                                                        <div>
                                                            <label for="edit-description-{{ $category->id }}" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                            <textarea id="edit-description-{{ $category->id }}" name="description" rows="2"
                                                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ $category->description }}</textarea>
                                                        </div>
                                                        <div class="flex justify-end space-x-2">
                                                            <button type="button" onclick="hideEditForm({{ $category->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                Save Changes
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                    No categories found. Create your first category to get started.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showEditForm(id) {
            document.getElementById('category-' + id).classList.add('hidden');
            document.getElementById('edit-form-' + id).classList.remove('hidden');
        }

        function hideEditForm(id) {
            document.getElementById('category-' + id).classList.remove('hidden');
            document.getElementById('edit-form-' + id).classList.add('hidden');
        }
    </script>
</x-app-layout>
