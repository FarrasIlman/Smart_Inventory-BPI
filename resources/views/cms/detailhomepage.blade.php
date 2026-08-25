@extends('layouts.main')
@section('hide_header', true)
@section('page_title', isset($log_id) && $log_id ? 'Edit Homepage' : 'Buat Homepage')

@section('content')

@php
    $initialHeroes = (isset($heroes) && count($heroes) > 0) ? $heroes : [['id' => null, 'title' => '', 'description' => '', 'position' => 'left', 'sort_order' => 1]];
@endphp

<div class="flex flex-col gap-5">
    <div class="flex flex-row items-center gap-2">
        <a href="{{ route('cms.log') }}" class="cursor-pointer">
            <i data-lucide="chevron-left" class="w-10 h-10"></i>
        </a>
        <p class="text-2xl font-bold">
            {{ isset($log_id) && $log_id ? 'Edit Homepage' : 'Buat Homepage' }}
        </p>
    </div>

    <div x-data="{ log_id: @js($log_id), heroes: @js($initialHeroes) }" class="shadow-md border border-gray-300 p-5 flex flex-col rounded-lg">
         {{-- section hero content --}}
        <div class="flex flex-row items-center justify-between">
            <p class="text-md font-semibold">Hero Content</p>
            <button @click="heroes.push({ id: null, title: '', description: '', position: 'left', sort_order: heroes.length + 1 })" class="flex flex-row items-center gap-2 rounded-lg bg-gray-700 cursor-pointer hover:bg-gray-900 px-4 py-2">
                <p class="text-sm text-white font-semibold">Tambah Slide</p>
                <i data-lucide="plus" class="w-5 h-5 text-white font-semibold"></i>
            </button>
        </div>

        <div></div>

    </div>
    
</div>

@endsection
