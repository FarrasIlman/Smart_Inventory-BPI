@extends('layouts.main')
@section('hide_header', true)
@section('page_title', isset($log_id) && $log_id ? 'Edit Homepage' : 'Buat Homepage')

@section('content')

    @php
        $initialHeroes = (isset($heroes) && count($heroes) > 0) ? $heroes : [
            [
                'id' => null,
                'title' => '',
                'description' => '',
                'position' => 'left',
                'image_id' => null,
                'image_mobile_id' => null,
                'is_masked' => false,
                'opacity' => 0,
                'sort_order' => 1
            ]
        ];

        $initialAbout = (isset($abouts) && $abouts->id) ? $abouts : [
            'id' => null, 
            'log_id' => null, 
            'title' => '', 
            'description' => '', 
            'bg_config' => [
                'type' => 'solid', 
                'colors' => ['#ffffff']
            ], 
            'metrics' => [
                [
                    'title' => '3+',
                    'description' => 'Tahun pengalaman'
                ]
            ]
        ];
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

        <form x-data="{ log_id: @js($log_id), heroes: @js($initialHeroes), about: @js($initialAbout) }" action="{{ route('cms.store') }}" method="POST" enctype="multipart/form-data">
            {{-- section hero content start --}}
            <div 
                class="shadow-md border border-gray-300 p-5 flex flex-col rounded-lg">
                <div class="flex flex-row items-center justify-between">
                    <p class="text-md font-semibold">Hero Content</p>
                    <button type="button"
                        @click="heroes.push({ id: null, title: '', description: '', position: 'left', image_mobile: '', mobile_public_id: '', image_url: '', image_public_id: '', is_masked: false, opacity: 0, sort_order: heroes.length + 1 })"
                        class="flex flex-row items-center gap-2 rounded-lg bg-gray-700 cursor-pointer hover:bg-gray-900 px-4 py-2">
                        <p class="text-sm text-white font-semibold">Tambah Slide</p>
                        <i data-lucide="plus" class="w-5 h-5 text-white font-semibold"></i>
                    </button>
                </div>

                <div class="flex flex-col gap-4 mt-4">
                    <template x-for="(hero, index) in heroes" :index="index">
                        <div class="shadow-md border border-gray-300 rounded-lg p-4 flex flex-col">
                            <div class="flex flex-row items-center justify-between">
                                <p class="text-sm font-semibold">Slide - <span x-text="index + 1"></span></p>

                                <button type="button" x-show="heroes.length > 1" @click="heroes.splice(index, 1)"
                                    class="px-4 py-2 bg-red-500 rounded-lg">
                                    <p class="text-white font-semibold text-sm">Hapus</p>
                                </button>
                            </div>

                            <div class="mt-4 flex flex-col gap-3">
                                <div class="flex flex-col gap-1">
                                    <label class="text-sm font-semibold">Judul Banner</label>
                                    <input type="text" :name="`heroes[${index}][title]`" x-model="hero.title"
                                        placeholder="Judul Banner..."
                                        class="p-2 text-sm text-gray-500 font-medium border border-gray-300 rounded-md focus:outline-none focus:border-blue-400"
                                        required>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-sm font-semibold">Deskripsi Banner</label>
                                    <input type="text" :name="`heroes[${index}][subtitle]`" x-model="hero.subtitle"
                                        placeholder="Deskripsi Banner..."
                                        class="p-2 text-sm text-gray-500 font-medium border border-gray-300 rounded-md focus:outline-none focus:border-blue-400"
                                        required>
                                </div>

                                {{-- Upload Image Desktop Start --}}
                                <div x-data="{ 
                                                        previewUrl: hero.image?.image_url || null,
                                                        fileName: hero.image?.file_name || null, 
                                                        fileSize: hero.image?.file_size ? (hero.image.file_size / 1024).toFixed(1) + ' KB' : null
                                                    }" class="flex flex-col gap-1">
                                    <label class="font-semibold text-sm">Upload Gambar Slide Hero</label>

                                    <div x-show="!previewUrl"
                                        class="p-4 relative border-2 border-dashed border-blue-500 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-100">
                                        <p class="text-sm text-gray-500 font-semibold">Upload Gambar</p>
                                        <input type="file" :name="`heroes[${index}][image]`" accept="image/*"
                                            class="absolute inset-0 z-20 opacity-0" @change="
                                                            const file = event.target.files[0];
                                                            if (file) {
                                                                previewUrl = URL.createObjectURL(file);
                                                                fileName = file.name;
                                                                fileSize = '';

                                                                if (file.size >= 1024) {
                                                                    fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB'
                                                                } else {
                                                                    fileSize = (file.size / 1024).toFixed(1) + ' KB'
                                                                }
                                                            }">
                                    </div>

                                    <template x-if="previewUrl">
                                        <div
                                            class="mt-2 flex flex-row items-center justify-between gap-3 p-4 shadow-md border border-gray-300">
                                            {{-- left section --}}
                                            <div class="flex flex-row gap-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                                    <path d="M14 2v4a1 1 0 0 0 1 1h4" />
                                                    <circle cx="10" cy="12" r="2" />
                                                    <path d="m20 17-1.296-1.296a2.41 2.41 0 0 0-3.408 0L9 22" />
                                                </svg>
                                                <div class="flex flex-col gap-2">
                                                    <a :href="previewUrl"
                                                        class="text-sm text-blue-500 font-semibold cursor-pointer"
                                                        target="_blank" x-text="fileName"></a>
                                                    <p class="text-xs text-gray-600 font-medium" x-text="fileSize"></p>
                                                </div>
                                            </div>

                                            {{-- right section --}}
                                            <button type="button"
                                                @click="previewUrl = null; fileName = null; fileSize = null;"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 cursor-pointer"
                                                title="Hapus Gambar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </button>

                                        </div>
                                    </template>
                                </div>
                                {{-- Upload Image Desktop End --}}

                                {{-- Upload Image Mobile Start --}}
                                <div x-data="{
                                                    previewMobileUrl: hero.image_mobile?.image_url || null, 
                                                    mobileFileName: hero.image_mobile?.file_name || null, 
                                                    mobileFileSize: hero.image_mobile?.file_size ? (hero.image.file_size / 1024).toFixed(1) + ' KB' : null
                                                }" class="flex flex-col gap-1">
                                    <label class="font-semibold text-sm">Upload Gambar Slide Hero Mobile</label>
                                    <div x-show="!previewMobileUrl"
                                        class="p-4 rounded-lg border-2 border-dashed border-blue-500 relative flex items-center justify-center hover:bg-gray-100">
                                        <input type="file" :name="`heroes[${index}][image_mobile]`" accept="image/*"
                                            class="absolute inset-0 z-20 opacity-0" @change="
                                                            const file = event.target.files[0];
                                                            previewMobileUrl = URL.createObjectURL(file);
                                                            mobileFileName = file.name;
                                                            mobileFileSize = '';

                                                            if (file.size >= 1024) {
                                                                mobileFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                                                            } else {
                                                                mobileFileSize = (file.size).toFixed(1) + ' KB';
                                                            }
                                                        ">
                                        <p class="text-sm font-semibold text-gray-500">Upload Gambar</p>
                                    </div>


                                    <div x-show="previewMobileUrl"
                                        class="shadow-md rounded-lg border border-gray-300 p-4 flex flex-row justify-between">
                                        {{-- left section --}}
                                        <div class="flex flex-row gap-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                                <path d="M14 2v4a1 1 0 0 0 1 1h4" />
                                                <circle cx="10" cy="12" r="2" />
                                                <path d="m20 17-1.296-1.296a2.41 2.41 0 0 0-3.408 0L9 22" />
                                            </svg>

                                            <div class="flex flex-col gap-2">
                                                <a :href="previewMobileUrl"
                                                    class="text-sm text-blue-500 cursor-pointer font-semibold"
                                                    x-text="mobileFileName"></a>
                                                <p x-text="mobileFileSize" class="text-xs font-semibold text-gray-500"></p>
                                            </div>
                                        </div>

                                        {{-- right section --}}
                                        <button type="button"
                                            @click="previewMobileUrl = null; mobileFileName = null; mobileFileSize = null;"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 cursor-pointer"
                                            title="Hapus Gambar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                </path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                {{-- Upload Image Mobile End --}}
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            {{-- section hero content end --}}


            {{-- section about us start --}}
            <div class="shadow-md border border-gray-300 p-5 rounded-lg flex flex-col mt-5">
                <div class="flex flex-row items-center justify-between">
                    <p class="font-semibold">About Us Content</p>
                </div>

                <div class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Judul</label>
                    <input type="text" x-model="about.title" name="about[title]" class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500" placeholder="Judul About Us...">
                </div>

                <div class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Deskripsi</label>
                    <input type="text" x-model="about.description" name="about[description]" class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500" placeholder="Judul About Us...">
                </div>

                <div class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Warna Background</label>
                    <select x-model="about.bg_config.type" @change="
                        if (about.bg_config.type === 'solid') {
                            about.bg_config.colors = [about.bg_config.colors[0] || '#ffffff'];
                        } else if (about.bg_config.type === 'gradient' && about.bg_config.colors.length < 2) {
                            about.bg_config.colors.push('#000000');
                        }
                    " class="p-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-lg focus:outline-none focus:border-gray-500">
                            <option value="solid">Solid</option>
                            <option value="gradient">Gradient</option>
                    </select>
                </div>

                <div x-show="about.bg_config.type === 'solid'" class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Pilih Warna</label>
                    <div class="flex flex-row items-center gap-2">
                        <input type="color" x-model="about.bg_config.colors[0]" class="w-10 h-10 rounded-lg border border-gray-300 p-1 cursor-pointer bg-white" />
                        <input type="text" x-model="about.bg_config.colors[0]" class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none" />
                    </div>
                </div>

                <div x-show="about.bg_config.type === 'gradient'" class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Pilih Warna Gradient</label>
                    <template x-for="(color, index) in about.bg_config.colors" :key="index">
                        <div class="flex flex-row items-center gap-2">
                            <input type="color" x-model="about.bg_config.colors[index]" class="w-10 h-10 rounded-lg border border-gray-300 p-1 cursor-pointer bg-white" />
                            <input type="text" x-model="about.bg_config.colors[index]" class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none" />
                            <button x-show="about.bg_config.colors.length > 1" type="button" class="px-4 py-2 bg-red-500 rounded-lg text-white text-sm" @click="about.bg_config.colors.splice(index, 1)">
                                Hapus
                            </button>
                        </div>
                    </template>
                    <button x-show="about.bg_config.colors.length < 3" type="button" class="px-4 py-2 rounded-lg bg-gray-700 text-sm font-semibold w-fit text-white mt-2" @click="about.bg_config.colors.push('#000000')">Tambah Warna</button>
                </div>
                <input type="hidden" name="about[bg_config]" :value="JSON.stringify(about.bg_config)" />

                <div class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Metriks Pencapaian Perusahaan</label>
                    <template x-for="(metric, index) in about.metrics" :key="index" class="flex flex-col gap-2">
                        <div class="flex flex-row gap-3 w-full text-sm items-center">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium">Judul Metrik</label>
                                <input type="text" x-model="about.metrics[index].title" class="p-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                            <div class="flex flex-col gap-2 w-[70%]">
                                <label class="text-sm font-medium">Deskripsi Metrik</label>
                                <input type="text" x-model="about.metrics[index].description" class="p-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                            <button x-show="about.metrics.length > 1" type="button" class="px-4 py-2 bg-red-500 rounded-lg text-white text-sm font-semibold mt-6" @click="about.metrics.splice(index, 1)">
                                Hapus
                            </button>
                        </div>
                    </template>
                    <button type="button" x-show="about.metrics.length < 3" class="px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-semibold w-fit mt-2" @click="about.metrics.push({title: '', description: ''})">Tambah Metrik</button>
                </div>
                <input type="hidden" name="about[metrics]" :value="JSON.stringify(about.metrics)" />
            </div>
            {{-- section about us end--}}


            {{-- submit button section --}}
            <div class="flex items-center justify-end mt-5">
                @if (!isset($log_id) && !$log_id)
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-700">
                        <p class="text-white text-sm font-semibold">Simpan</p>
                    </button>
                @endif
            </div>
        </form>


    </div>

@endsection