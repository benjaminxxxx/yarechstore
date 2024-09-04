<x-blank-layout>

    <div class="py-12 bg-slate-100 h-screen flex items-center justify-center">

        <form method="POST" action="{{ route('set-branch') }}">
            @csrf
            <x-h3 class="mb-5 text-lg font-medium text-gray-900 dark:text-white">{{ __('Select Branch') }}</x-h3>
            <x-label>{{ __('You must choose a branch to start sales') }}</x-label>
            <div class="border-dashed border-2 border-primary p-6 rounded-lg mb-4">
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($branches as $branch)
                        <x-selection-branch :branchCode="$branch->code" :branchName="$branch->name" :branchAddress="$branch->address" />
                    @endforeach
                </div>
            </div>
            <x-button type="submit" class="w-full text-center justify-center">
                {{ __('Select') }}
            </x-button>
        </form>
    </div>
</x-blank-layout>
