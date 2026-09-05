@extends('layouts.main')
@section('page_title', 'Log CMS Homepage')
@section('content')

<div class="flex flex-col gap-5">
    <div class="flex justify-end">
        <a href="{{ route('cms.create') }}" class="px-4 py-2 flex flex-row gap-2 bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-900">
            <p class="text-white font-semibold text-sm">Buat</p>
            <i data-lucide="plus" class="w-5 h-5 text-white"></i>
        </a>
    </div>

    {{-- table log --}}
    <div class="shadow-md border border-gray-200 w-full rounded-md overflow-hidden">
        <table class="w-full">
            <thead class="h-10 text-white bg-gray-700 text-sm w-full">
                <tr class="w-full">
                    <th class="w-1/8">No</th>
                    <th class="w-2/8 text-left">Status</th>
                    <th class="w-3/8 text-left">Catatan</th>
                    <th class="w-3/8 text-left">Tanggal</th>
                    <th class="w-1/8 text-center pr-10">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if (count($logs) == 0)
                    <tr>
                        <td colspan="5">
                            <div class="flex items-center justify-center p-4">
                                <p class="text-sm text-gray-500 font-semibold">Belum ada logs tercatat saat ini</p>
                            </div>
                        </td>
                    </tr>
                @endif

                @foreach ($logs as $index => $log)
                    <tr class="border-b border-b-gray-200 text-sm h-12">
                        <td class="w-1/8 text-center">
                            <p>{{ $index + 1 }}</p>
                        </td>
                        <td class="w-2/8 text-left">
                            <p class="p-2 w-fit text-xs font-semibold rounded-lg {{ $log->status_color }}">{{ $log->status_label }}</p>
                        </td>
                        <td class="w-3/8 text-left">
                            <p class="line-clamp-1">{{ $log->notes }}</p>
                        </td>
                        <td class="w-1/8 text-left">
                            <p class="text-xs font-semibold">{{ $log->converted_date }}</p>
                        </td>
                        <td class="w-1/8 text-center pr-10">
                            <div class="flex items-center justify-center cursor-pointer">
                                <i data-lucide="square-pen" class="w-5 h-5 text-blue-500"></i>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@endsection

