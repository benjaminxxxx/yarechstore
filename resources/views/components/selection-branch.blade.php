@props(['branchCode', 'branchName', 'branchAddress'])

<div>
    <input type="radio" id="select-option{{ $branchCode }}" value="{{ $branchCode }}"
        name="branch_code" required class="hidden peer">
    <label for="select-option{{ $branchCode }}"
        class="block w-full p-5 text-gray-500 bg-white border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:!text-white hover:text-white hover:bg-indigo-400">
        <div class="block text-center">
            <i class="fa fa-store text-6xl"></i>
            <h3 class="uppercase mt-2 text-sm font-semibold">{{ $branchName }}</h3>
            <p class="text-xs">{{ $branchAddress }}</p>
        </div>
    </label>
</div>