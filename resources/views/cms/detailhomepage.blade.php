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
                'image' => null,
                'image_mobile' => null,
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
            ],
            'image' => null

        ];

        $initialService = (isset($service) && $service->id) ? $service : [
            'id' => null,
            'log_id' => null,
            'title' => '',
            'description' => '',
            'bg_config' => [
                'type' => 'solid',
                'colors' => ["#ffffff"]
            ],
            'details' => [
                [
                    'id' => null,
                    'service_id' => null,
                    'name' => '',
                    'description' => '',
                    'sort_order' => 1,
                    'image_id' => null

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

        <form x-data="{ log_id: @js($log_id), heroes: @js($initialHeroes), about: @js($initialAbout), service: @js($initialService), showModal: false, isSubmitting: false, notes: '' }" @submit="isSubmitting = true" action="{{ route('cms.store') }}" method="POST" enctype="multipart/form-data">
            {{-- section hero content start --}}
            <div class="shadow-md border border-gray-300 p-5 flex flex-col rounded-lg">
                <div class="flex flex-row items-center justify-between">
                    <p class="text-md font-semibold">Hero Content</p>
                    <button type="button"
                        @click="heroes.push({ id: null, title: '', description: '', position: 'left', image_mobile: null, image: null, is_masked: false, opacity: 0, sort_order: heroes.length + 1 })"
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
                                        <p class="text-sm text-blue-500 font-semibold">Upload Gambar</p>
                                        <input type="file" :name="`heroes[${index}][image]`" accept="image/*"
                                            class="absolute inset-0 z-20 opacity-0" @change="
                                                                                const file = event.target.files[0];
                                                                                if (file) {
                                                                                    previewUrl = URL.createObjectURL(file);
                                                                                    fileName = file.name;
                                                                                    fileSize = '';

                                                                                    if (file.size >= 1024) {
                                                                                        fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                                                                                    } else {
                                                                                        fileSize = (file.size / 1024).toFixed(1) + ' KB';
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
                                        <p class="text-sm font-semibold text-blue-500">Upload Gambar</p>
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
                    <input type="text" x-model="about.title" name="about[title]"
                        class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500"
                        placeholder="Judul About Us...">
                </div>

                <div class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Deskripsi</label>
                    <input type="text" x-model="about.description" name="about[description]"
                        class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500"
                        placeholder="Judul About Us...">
                </div>

                <div class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Warna Background</label>
                    <select x-model="about.bg_config.type" @change="
                                            if (about.bg_config.type === 'solid') {
                                                about.bg_config.colors = [about.bg_config.colors[0] || '#ffffff'];
                                            } else if (about.bg_config.type === 'gradient' && about.bg_config.colors.length < 2) {
                                                about.bg_config.colors.push('#000000');
                                            }
                                        "
                        class="p-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-lg focus:outline-none focus:border-gray-500">
                        <option value="solid">Solid</option>
                        <option value="gradient">Gradient</option>
                    </select>
                </div>

                <div x-show="about.bg_config.type === 'solid'" class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Pilih Warna</label>
                    <div class="flex flex-row items-center gap-2">
                        <input type="color" x-model="about.bg_config.colors[0]"
                            class="w-10 h-10 rounded-lg border border-gray-300 p-1 cursor-pointer bg-white" />
                        <input type="text" x-model="about.bg_config.colors[0]"
                            class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none" />
                    </div>
                </div>

                <div x-show="about.bg_config.type === 'gradient'" class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Pilih Warna Gradient</label>
                    <template x-for="(color, index) in about.bg_config.colors" :key="index">
                        <div class="flex flex-row items-center gap-2">
                            <input type="color" x-model="about.bg_config.colors[index]"
                                class="w-10 h-10 rounded-lg border border-gray-300 p-1 cursor-pointer bg-white" />
                            <input type="text" x-model="about.bg_config.colors[index]"
                                class="p-2 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none" />
                            <button x-show="about.bg_config.colors.length > 1" type="button"
                                class="px-4 py-2 bg-red-500 rounded-lg text-white text-sm" @click="
                                                        about.bg_config.colors.splice(index, 1);
                                                        about.bg_config.type === 'solid';
                                                    ">
                                Hapus
                            </button>
                        </div>
                    </template>
                    <button x-show="about.bg_config.colors.length < 3" type="button"
                        class="px-4 py-2 rounded-lg bg-gray-700 text-sm font-semibold w-fit text-white mt-2"
                        @click="about.bg_config.colors.push('#000000')">Tambah Warna</button>
                </div>
                <input type="hidden" name="about[bg_config]" :value="JSON.stringify(about.bg_config)" />

                <div x-data="{
                                        aboutUrl: about.image?.image_url || null,
                                        aboutFileName: about.image?.file_name || null,
                                        aboutFileSize: about.image?.file_size ? (about.image.file_size / 1024).toFixed(2) + ' KB' : null
                                    }" class="mt-4">

                    <div x-show="!aboutUrl" class="flex flex-col gap-2">
                        <label class="font-semibold text-sm">Gambar Tentang Kita</label>
                        <div
                            class="rounded-lg border-2 border-dashed border-blue-500 relative flex items-center justify-center p-5">
                            <input type="file" name="about[image]" accept="image/*"
                                class="absolute inset-0 opacity-0 cursor-pointer" @change="
                                                    const file = event.target.files[0];
                                                    if (file) {
                                                        aboutUrl = URL.createObjectURL(file);
                                                        aboutFileName = file.name;
                                                        aboutFileSize = ''

                                                        if (file.size > 1024) {
                                                            aboutFileSize = (file.size / (1024 * 1024)).toFixed(1) + ' KB';
                                                        } else {
                                                            aboutFileSize = (file.size /  1024).toFixed(2) + ' MB';
                                                        }
                                                    }
                                                " />
                            <p class="text-sm font-semibold text-blue-500">Upload Gambar</p>
                        </div>
                    </div>

                    <div x-show="aboutUrl"
                        class="p-2 shadow-md flex flex-row items-center justify-between rounded-lg border border-gray-300">
                        <div class="flex flex-row gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                <path d="M14 2v4a1 1 0 0 0 1 1h4" />
                                <circle cx="10" cy="12" r="2" />
                                <path d="m20 17-1.296-1.296a2.41 2.41 0 0 0-3.408 0L9 22" />
                            </svg>
                            <div class="flex flex-col gap-2">
                                <a :href="aboutUrl" target="_blank" x-text="aboutFileName"
                                    class="text-sm font-semibold text-blue-500 cursor-pointer"></a>
                                <p class="text-xs font-medium text-gray-500" x-text="aboutFileSize"></p>
                            </div>
                        </div>
                        <button type="button" @click="aboutUrl = null; aboutFileName = null; aboutFileSize = null;"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 cursor-pointer"
                            title="Hapus Gambar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    </div>
                </div>



                <div class="flex flex-col gap-1 mt-4">
                    <label class="text-sm font-semibold">Metriks Pencapaian Perusahaan</label>
                    <template x-for="(metric, index) in about.metrics" :key="index" class="flex flex-col gap-2">
                        <div class="flex flex-row gap-3 w-full text-sm items-center">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium">Judul Metrik</label>
                                <input type="text" x-model="about.metrics[index].title"
                                    class="p-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                            <div class="flex flex-col gap-2 w-[70%]">
                                <label class="text-sm font-medium">Deskripsi Metrik</label>
                                <input type="text" x-model="about.metrics[index].description"
                                    class="p-2 border border-gray-300 rounded-lg text-sm" />
                            </div>
                            <button x-show="about.metrics.length > 1" type="button"
                                class="px-4 py-2 bg-red-500 rounded-lg text-white text-sm font-semibold mt-6"
                                @click="about.metrics.splice(index, 1)">
                                Hapus
                            </button>
                        </div>
                    </template>
                    <button type="button" x-show="about.metrics.length < 3"
                        class="px-4 py-2 rounded-lg bg-gray-700 text-white text-sm font-semibold w-fit mt-2"
                        @click="about.metrics.push({title: '', description: ''})">Tambah Metrik</button>
                </div>
                <input type="hidden" name="about[metrics]" :value="JSON.stringify(about.metrics)" />
            </div>
            {{-- section about us end--}}

            {{-- Section services start --}}
            <div class="mt-5 flex flex-col shadow-md border border-gray-300 rounded-lg p-5">
                <p class="font-semibold">Konten Layanan Kami</p>

                <div class="flex flex-col gap-2 mt-4">
                    <label class="font-semibold text-sm">Judul</label>
                    <input type="text" x-model="service.title" name="service[title]"
                        class="p-2 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500" />
                </div>

                <div class="flex flex-col gap-2 mt-4">
                    <label class="font-semibold text-sm">Deskripsi</label>
                    <input type="text" x-model="service.description" name="service[description]"
                        class="p-2 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

                <div class="flex flex-col gap-2 mt-4">
                    <label class="text-sm font-semibold">Tipe Background</label>
                    <select x-model="service.bg_config.type"
                        class="p-2 rounded-lg border border-gray-300 focus:outline-none focus:border-gray-500 text-sm"
                        @change="
                                            if (service.bg_config.type === 'solid') {
                                                service.bg_config.colors = [service.bg_config.colors[0] || '#ffffff'];
                                            } else if (service.bg_config.type === 'gradient' && service.bg_config.colors.length < 2) {
                                                service.bg_config.colors.push('#000000');
                                            }
                                        ">
                        <option value="solid">Solid</option>
                        <option value="gradient">Gradient</option>
                    </select>
                </div>

                <div x-show="service.bg_config.type === 'solid'" class="mt-4 flex flex-col gap-2">
                    <label class="font-semibold text-sm">Pilih Warna</label>
                    <div class="flex flex-row items-center gap-2">
                        <input type="color" x-model="service.bg_config.colors[0]"
                            class="w-10 h-10 p-1 border border-gray-300 rounded-lg">
                        <input type="text" x-model="service.bg_config.colors[0]"
                            class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-gray-300 text-sm">
                    </div>
                </div>

                <div x-show="service.bg_config.type === 'gradient'" class="flex flex-col gap-1 mt-4">
                    <template x-for="(color, index) in service.bg_config.colors" :key="index">
                        <div class="flex flex-row items-center gap-2">
                            <input type="color" x-model="service.bg_config.colors[index]"
                                class="w-10 h-10 p-1 border border-gray-300 rounded-lg">
                            <input type="text" x-model="service.bg_config.colors[index]"
                                class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-gray-300 text-sm">
                            <button x-show="service.bg_config.colors.length > 1" type="button"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg font-semibold text-sm" @click="
                                                    service.bg_config.colors.splice(index, 1);
                                                    if (service.bg_config.colors.length < 2) {
                                                        service.bg_config.type = 'solid';
                                                    }
                                                ">Hapus</button>
                        </div>
                    </template>
                    <button type="button" x-show="service.bg_config.colors.length < 3"
                        class="mt-2 w-fit p-2 bg-gray-700 rounded-lg text-white font-semibold text-sm"
                        @click="service.bg_config.colors.push('#000000')">
                        Tambah Warna
                    </button>
                </div>

                <input type="hidden" name="service[bg_config]" :value="JSON.stringify(service.bg_config)">

                <div class="mt-4 flex flex-col gap-2">
                    <div class="flex flex-row items-center justify-between">
                        <label class="text-sm font-bold">Detail Layanan Kami</label>
                        <button type="button" class="text-sm font-semibold text-white bg-gray-700 px-4 py-2 rounded-lg"
                            @click="
                                                service.details.push({name: '', description: '', image_id: null});
                                            ">
                            Tambah Detail
                        </button>
                    </div>

                    <template x-for="(detail, index) in service.details" :key="index">
                        <div class="p-4 shadow-md border border-gray-300 rounded-lg flex flex-col mb-2">
                            <div class="flex flex-row items-center justify-between">
                                <p class="text-sm font-bold">Layanan - <span x-text="index + 1"></span></p>
                                <button x-show="service.details.length > 1"
                                    class="px-4 py-2 rounded-lg bg-red-500 text-white font-semibold text-sm"
                                    @click="service.details.splice(index, 1)">
                                    Hapus
                                </button>
                            </div>
                            <div class="flex flex-col gap-1 mt-2">
                                <label class="text-sm font-semibold">Nama Layanan</label>
                                <input type="text" x-model="detail.name" :name="`service[details][${index}][name]`"
                                    class="p-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-gray-500">
                            </div>
                            <div class="flex flex-col gap-1 mt-2">
                                <label class="text-sm font-semibold">Deskripsi Layanan</label>
                                <textarea x-model="detail.description" :name="`service[details][${index}][description]`"
                                    class="p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-gray-500"></textarea>
                            </div>
                            <div x-data="{
                                                    serviceImgUrl: detail.image?.image_url || null, 
                                                    serviceFileName: detail.image?.file_name || null, 
                                                    serviceFileSize: detail.image?.file_size ? (detail.image.file_size / 1024).toFixed(1) + ' KB' : null
                                                }" class="flex flex-col gap-1 mt-2">
                                <p class="text-sm font-semibold">Gambar Layanan</p>
                                <div x-show="!serviceImgUrl"
                                    class="rounded-lg border-2 border-dashed border-blue-500 flex items-center justify-center relative p-4 hover:bg-gray-200">
                                    <input type="file" x-model="detail.image" :name="`service[details][${index}][image]`"
                                        accept="image/*" class="absolute z-20 inset-0 opacity-0" @change="
                                                            const file = event.target.files[0];

                                                            serviceImgUrl =  URL.createObjectURL(file);
                                                            serviceFileName =  file.name;
                                                            serviceFileSize = '';

                                                            if (file.size >= 1024) {
                                                                serviceFileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                                                            } else { 
                                                                serviceFileSize = (file.size / 1024).toFixed(1) + ' KB';
                                                            }
                                                        ">
                                    <p class="text-sm font-semibold text-blue-500">Upload Gambar</p>
                                </div>

                                <div x-show="serviceImgUrl"
                                    class="p-4 shadow-md border border-gray-300 flex flex-row items-center justify-between">
                                    <div class="flex flex-row gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                                            <path d="M14 2v4a1 1 0 0 0 1 1h4" />
                                            <circle cx="10" cy="12" r="2" />
                                            <path d="m20 17-1.296-1.296a2.41 2.41 0 0 0-3.408 0L9 22" />
                                        </svg>
                                        <div class="flex flex-col gap-2">
                                            <a :href="serviceImgUrl" target="_blank" class="text-sm text-blue-500"
                                                x-text="serviceFileName"></a>
                                            <p class="text-xs text-gray-500" x-text="serviceFileSize"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="
                                                            serviceImgUrl = null;
                                                            serviceFileName = null;
                                                            serviceFileSize = null;
                                                        "
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
                                        </svg>>

                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            {{-- Section services end --}}


            {{-- submit button section --}}
            <div class="flex items-center justify-end mt-5">
                @if (!isset($log_id) && !$log_id)
                    <button type="button" @click="showModal = true" class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-700">
                        <p class="text-white text-sm font-bold">Simpan</p>
                    </button>
                @endif
            </div>

            {{-- modal section start --}}

            <div x-show="showModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
                <div class="min-w-md bg-white rounded-lg shadow-md flex flex-col p-4">
                    <h3 class="text-lg font-bold text-gray-700">Konfirmasi Perubahan</h3>
                    <div class="flex flex-col gap-2 mt-2">
                        <label class="text-sm font-semibold text-gray-700">Catatan Perubahan (Notes)</label>
                        <textarea x-model="notes" name="notes"
                            class="p-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-gray-500"
                            placeholder="Catatan perubahan"></textarea>
                    </div>
                    <div class="flex flex-row items-center justify-center gap-3 mt-2">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-red-500 text-white font-semibold text-sm rounded-lg hover:bg-red-700">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-gray-700 text-sm font-semibold text-white hover:bg-gray-900">Simpan</button>
                    </div>
                </div>
            </div>

            {{-- modal section end --}}

            {{-- modal loading spinner start --}}
            <div x-show="isSubmitting" x-cloak class="fixed inset-0 bg-black/60 z-50 flex flex-col items-center justify-center gap-3">
                <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-white font-semibold text-base animate-pulse">Menyimpan data, mohon tunggu...</p>
            </div>
            {{-- modal loading spinner end --}}
        </form>


    </div>

@endsection