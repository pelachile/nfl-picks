<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
    <!-- Success Message -->
    @if($showSuccessMessage && $createdGroup)
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-green-800">Group Created Successfully!</h3>
                <div class="mt-2 text-sm text-green-700">
                    <p><strong>{{ $createdGroup->name }}</strong> has been created.</p>
                    <p class="mt-1">Invite Code: <span class="font-mono font-bold">{{ $createdGroup->invite_code }}</span></p>
                    <p class="text-xs mt-1">Share this code with friends to join your group!</p>
                </div>
                <div class="mt-3">
                    <button wire:click="dismissSuccess" class="text-sm bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200">
                        Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <form wire:submit="createGroup" class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Create New Group</h2>
            <p class="text-gray-600 text-sm">Start your own NFL picks competition!</p>
        </div>

        <!-- Group Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Group Name <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="name"
                wire:model="name"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                placeholder="e.g., Office League, Fantasy Friends"
                maxlength="50"
            >
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Description (Optional)
            </label>
            <textarea
                id="description"
                wire:model="description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Tell people what this group is about..."
                maxlength="255"
            ></textarea>
            @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Max Members -->
        <div>
            <label for="maxMembers" class="block text-sm font-medium text-gray-700 mb-1">
                Maximum Members
            </label>
            <select
                id="maxMembers"
                wire:model="maxMembers"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
                <option value="5">5 Members</option>
                <option value="10">10 Members</option>
                <option value="15">15 Members</option>
                <option value="20" selected>20 Members</option>
                <option value="25">25 Members</option>
                <option value="30">30 Members</option>
                <option value="50">50 Members</option>
            </select>
            @error('maxMembers')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Public/Private Toggle -->
        <div class="flex items-center">
            <input
                type="checkbox"
                id="isPublic"
                wire:model="isPublic"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            >
            <label for="isPublic" class="ml-2 block text-sm text-gray-700">
                Make this group discoverable by others
            </label>
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-75 cursor-not-allowed"
            wire:target="createGroup"
        >
            <span wire:loading.remove>Create Group</span>
            <span wire:loading class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Creating Group...
            </span>
        </button>
    </form>

    <!-- Error Message -->
    @if(session('error'))
    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
        <p class="text-sm text-red-600">{{ session('error') }}</p>
    </div>
    @endif
</div>
