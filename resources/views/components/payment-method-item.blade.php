<div {{ $attributes->merge(['class' => 'flex items-center justify-between p-5 rounded shadow cursor-pointer']) }} 
     data-id="{{ $id }}"
>
    <div class="flex items-center">
        <i class="fa {{ $icon }} mr-2"></i>
        <span>{{ $label }}</span>
    </div>
    {{$slot}}
</div>
