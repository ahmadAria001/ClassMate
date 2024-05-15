<script lang="ts">
    import { onMount } from "svelte";
    import { page, router } from "@inertiajs/svelte";
    import { sineIn } from "svelte/easing";
    import {
        Button,
        DarkMode,
        Navbar,
        NavBrand,
        NavHamburger,
        Sidebar,
        SidebarGroup,
        SidebarItem,
        SidebarWrapper,
        Drawer,
        CloseButton,
        SidebarDropdownWrapper,
        Avatar,
        Dropdown,
        DropdownHeader,
        DropdownItem,
        DropdownDivider,
        Listgroup,
        ListgroupItem,
        Footer,
        FooterCopyright,
    } from "flowbite-svelte";
    import {
        ChartPieSolid,
        UsersGroupSolid,
        FileSolid,
        ClipboardSolid,
        VolumeUpSolid,
        BellSolid,
    } from "flowbite-svelte-icons";
    import { twMerge } from "tailwind-merge";
    import { writable } from "svelte/store";
    import axiosInstance from "axios";

    const url = writable("");
    onMount(() => {
        url.set(window.location.pathname);
    });

    const axios = axiosInstance.create({ withCredentials: true });

    // tempat rolenya disini
    // console.log($page.props);
    let role = $page.props.auth.user.role;

    let filtermenu: {
        name: string;
        icon: any;
        isOpenItems?: boolean;
        children?: Record<string, string>;
        href?: string;
    }[] = [];

    if (role == "RT") {
        filtermenu = [
            { name: "Beranda", icon: ChartPieSolid, href: "/beranda" },
            {
                name: "Data Warga",
                icon: UsersGroupSolid,
                isOpenItems: true,
                children: {
                    "Lihat Data Warga": "/warga-rt",
                    "Arsip Warga": "/arsip-penduduk",
                },
            },
            {
                name: "Pengaduan",
                icon: FileSolid,
                isOpenItems: true,
                children: {
                    "Daftar Pengaduan": "/daftar-pengaduan",
                },
            },
            {
                name: "Laporan",
                icon: ClipboardSolid,
                isOpenItems: true,
                children: {
                    "Surat Ket. Warga": "/daftar-pengajuan-surat",
                    "Laporan keuangan": "/keuangan",
                    "Bantuan sosial": "/daftar-bansos",
                },
            },
            {
                name: "Informasi",
                icon: VolumeUpSolid,
                isOpenItems: true,
                children: {
                    Pengumuman: "/pengumuman",
                    "Kegiatan Warga": "/kegiatan-warga",
                },
            },
        ];
    } else if (role == "RW") {
        filtermenu = [
            { name: "Beranda", icon: ChartPieSolid, href: "/beranda" },
            {
                name: "Data Warga",
                icon: UsersGroupSolid,
                isOpenItems: true,
                children: {
                    "Lihat Data Warga": "/daftar-rt",
                    "Arsip Warga": "/arsip-penduduk",
                },
            },
            {
                name: "Pengaduan",
                icon: FileSolid,
                isOpenItems: true,
                children: {
                    "Daftar Pengaduan": "/daftar-pengaduan",
                },
            },
            {
                name: "Laporan",
                icon: ClipboardSolid,
                isOpenItems: true,
                children: {
                    "Surat Ket. Warga": "/daftar-pengajuan-surat",
                    "Bantuan sosial": "/daftar-bansos",
                },
            },
            {
                name: "Informasi",
                icon: VolumeUpSolid,
                isOpenItems: true,
                children: {
                    Pengumuman: "/pengumuman",
                    "Kegiatan Warga": "/kegiatan-warga",
                },
            },
        ];
    } else if (role == "Admin") {
        filtermenu = [
            { name: "Beranda", icon: ChartPieSolid, href: "/beranda" },
            {
                name: "Data Warga",
                icon: UsersGroupSolid,
                isOpenItems: true,
                children: {
                    "Lihat Data Warga": "/warga-rt",
                    "Lihat Data Warga RT": "/daftar-rt",
                    "Arsip Warga": "/arsip-penduduk",
                },
            },
            {
                name: "Pengaduan",
                icon: FileSolid,
                isOpenItems: true,
                children: {
                    "Daftar Pengaduan": "/daftar-pengaduan",
                    "Status Pengaduan warga": "/status-pengaduan",
                },
            },
            {
                name: "Laporan",
                icon: ClipboardSolid,
                isOpenItems: true,
                children: {
                    "Surat Ket. Warga": "/daftar-pengajuan-surat",
                    "Bantuan sosial": "/daftar-bansos",
                },
            },
            {
                name: "Surat",
                icon: ClipboardSolid,
                isOpenItems: true,
                children: {
                    "Status Surat": "/status-pengajuan",
                },
            },
            {
                name: "Informasi",
                icon: VolumeUpSolid,
                isOpenItems: true,
                children: {
                    Pengumuman: "/pengumuman",
                    "Kegiatan Warga": "/kegiatan-warga",
                },
            },
            {
                name: "Bantuan Sosial",
                icon: VolumeUpSolid,
                isOpenItems: true,
                children: {
                    "Status Bantuan Sosial": "/status-bansos",
                },
            },
        ];
    } else {
        filtermenu = [
            { name: "Beranda", icon: ChartPieSolid, href: "/beranda" },
            {
                name: "Pengaduan",
                icon: FileSolid,
                isOpenItems: true,
                children: {
                    "Status pengaduan": "/status-pengaduan",
                },
            },
            {
                name: "Surat",
                icon: ClipboardSolid,
                isOpenItems: true,
                children: {
                    "Status Surat": "/status-pengajuan",
                },
            },
            {
                name: "Bantuan Sosial",
                icon: VolumeUpSolid,
                isOpenItems: true,
                children: {
                    "Status Bantuan Sosial": "/status-bansos",
                },
            },
        ];
    }

    export let active: string = "";

    let site = {
        name: "KawanDesa",
        img: "assets/icons/KD_logo.svg",
        link: "/",
        imgAlt: "KawanDesa Logo",
    };

    let itemClass =
        "flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 group dark:text-gray-200 dark:hover:bg-gray-700";
    let groupClass = "pt-2 space-y-2";

    let transitionParams = {
        x: -320,
        duration: 200,
        easing: sineIn,
    };

    const getKeyByValue = (object: any, value: string) => {
        return Object.keys(object).find((key: any) => object[key] === value);
    };

    let breakPoint: number = 1024;
    let width: number;
    let backdrop: boolean = false;
    let activateClickOutside = true;
    let drawerHidden: boolean = false;
    $: if (width >= breakPoint) {
        drawerHidden = false;
        activateClickOutside = false;
    } else {
        drawerHidden = true;
        activateClickOutside = true;
    }
    onMount(() => {
        if (width >= breakPoint) {
            drawerHidden = false;
            activateClickOutside = false;
        } else {
            drawerHidden = true;
            activateClickOutside = true;
        }
    });
    const toggleSide = () => {
        if (width < breakPoint) {
            drawerHidden = !drawerHidden;
        }
    };
    const toggleDrawer = () => {
        // console.log("test");
        // drawerHidden = !drawerHidden;
        drawerHidden = false;
    };

    let notifs = [
        {
            id: 1,
            img: "assets/icons/KD_logo.svg",
            headline: "Laporan Pengaduan",
            desc: "Laporan anda sedang tahap proses",
            time: "10 minutes ago",
        },
        {
            id: 2,
            img: "assets/icons/KD_logo.svg",
            headline: "Pengumuman",
            desc: "Dimohon kehadiran dan partisinya",
            time: "12 minutes ago",
        },
    ];

    const logout = async () => {
        const { data, status } = await axios.get("/api/auth/signout");

        if (status != 200) {
            router.visit("/login");
            return;
        }

        router.visit("/login");
    };
</script>

<svelte:window bind:innerWidth={width} />
<header class="flex-none w-full mx-auto bg-white dark:bg-slate-950">
    <Navbar let:hidden let:toggle class="border-b-2 h-16 fixed">
        <NavHamburger
            onClick={toggleDrawer}
            btnClass="focus:outline-none whitespace-normal rounded-lg focus:ring-2 p-1.5 focus:ring-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 m-0 mr-3 lg:hidden"
        />
        <NavBrand href="/" class="md:w-64">
            <svg
                version="1.1"
                id="Layer_1"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                x="0px"
                y="0px"
                viewBox="0 0 1000 1000"
                style="enable-background:new 0 0 1000 1000;"
                xml:space="preserve"
                class="fill-dark dark:fill-white h-6 md:h-8 mr-3"
            >
                <style type="text/css">
                    .st0 {
                        stroke: #000000;
                        stroke-miterlimit: 10;
                    }
                </style>
                <g>
                    <g>
                        <path
                            class="st0"
                            d="M990,499.16c0,42.31-5.36,83.37-15.45,122.55c-11.18,43.47-28.19,84.61-50.13,122.55
                        c-16.36,28.29-35.47,54.79-56.97,79.15c-13.55,15.35-28.05,29.85-43.4,43.4c-24.36,21.5-50.86,40.61-79.15,56.97
                        c-37.94,21.94-79.08,38.94-122.55,50.13c-37.56,9.67-63.32,15.03-102.66,15.46c-2.85,0.03-5.19-2.26-5.19-5.11V871.9
                        c0-2.79,2.24-5.06,5.03-5.1c40.07-0.56,66-7.91,102.82-20.92c45.67-16.15,87.2-41.07,122.55-72.73
                        c10.18-9.07,19.83-18.72,28.9-28.9c31.66-35.35,56.58-76.88,72.73-122.55c13.55-38.33,20.92-79.57,20.92-122.55
                        s-7.37-84.22-20.92-122.55c-16.15-45.67-41.07-87.2-72.73-122.55c-9.07-10.18-18.72-19.83-28.9-28.9
                        c-35.35-31.66-76.88-56.58-122.55-72.73c-36.81-13.01-62.75-20.29-102.81-20.85c-2.79-0.04-5.03-2.31-5.03-5.1l0-112.35
                        c0-2.85,2.34-5.14,5.19-5.11c39.34,0.42,65.1,5.72,102.66,15.39C665.83,35.6,706.97,52.6,744.9,74.54
                        c28.29,16.36,54.79,35.47,79.15,56.97c15.35,13.55,29.85,28.05,43.4,43.4c21.5,24.36,40.61,50.86,56.97,79.15
                        c21.94,37.94,38.94,79.08,50.13,122.55C984.64,415.79,990,456.84,990,499.16z"
                        />
                        <path
                            class="st0"
                            d="M744.9,499.16c0,44.64-11.95,86.5-32.8,122.55c-21.51,37.21-52.54,68.24-89.75,89.75
                        c-34.62,20.02-61.03,31.87-102.6,32.78c-2.87,0.06-5.25-2.24-5.25-5.11V626.73c0-2.73,2.16-4.98,4.89-5.09
                        c64.72-2.58,102.96-56.46,102.96-122.49c0-66.03-38.24-119.84-102.96-122.42c-2.73-0.11-4.89-2.36-4.89-5.09V259.26
                        c0-2.87,2.38-5.17,5.25-5.11c41.57,0.91,67.98,12.69,102.6,32.71c37.21,21.51,68.24,52.54,89.75,89.75
                        C732.96,412.66,744.9,454.51,744.9,499.16z"
                        />
                    </g>
                    <g>
                        <g>
                            <path
                                class="st0"
                                d="M433.61,728.97c-16.36-28.29-35.47-54.79-56.97-79.15c-13.55-15.35-28.05-29.85-43.4-43.4
                            c-24.36-21.5-50.86-40.61-79.15-56.97c-37.94-21.94-79.08-38.94-122.55-50.13c-0.18-0.05-0.36-0.09-0.55-0.14
                            c0.18-0.05,0.36-0.09,0.55-0.14c43.47-11.18,84.61-28.19,122.55-50.13c28.29-16.36,54.79-35.47,79.15-56.97
                            c15.35-13.55,29.85-28.05,43.4-43.4c21.5-24.36,40.61-50.86,56.97-79.15c21.94-37.94,38.94-79.08,50.13-122.55
                            c9.69-37.61,15.02-91.05,15.43-132.7c0.03-2.85-2.26-5.17-5.11-5.17H381.71c-2.78,0-5.07,2.23-5.1,5.01
                            c-0.54,42.28-7.86,96-20.89,132.85c-16.15,45.67-41.07,87.2-72.73,122.55c-9.07,10.18-18.72,19.83-28.9,28.9
                            c-35.35,31.66-76.88,56.58-122.55,72.73c-36.82,13.02-76.33,20.33-117.48,20.88c-2.8,0.04-5.06,2.29-5.06,5.1v86.85v30.64v86.85
                            c0,2.8,2.26,5.06,5.06,5.1c41.15,0.56,80.66,7.87,117.48,20.88c45.67,16.15,87.2,41.07,122.55,72.73
                            c10.18,9.07,19.83,18.72,28.9,28.9c31.66,35.35,56.58,76.88,72.73,122.55c13.03,36.86,20.34,90.57,20.89,132.85
                            c0.04,2.78,2.32,5.01,5.1,5.01h112.34c2.85,0,5.14-2.32,5.11-5.17c-0.41-41.65-5.74-95.09-15.43-132.7
                            C472.55,808.05,455.55,766.91,433.61,728.97z"
                            />
                            <path
                                class="st0"
                                d="M131.55,761.77c-34.66-20.04-74.68-31.86-117.38-32.75c-2.84-0.06-5.16,2.27-5.16,5.11v112.42
                            c0,2.72,2.14,4.95,4.86,5.09c63.81,3.17,115.1,68.37,117.59,132.86c0.11,2.73,2.35,4.89,5.09,4.89h112.39
                            c2.88,0,5.17-2.37,5.11-5.24c-0.89-43.8-12.7-97.97-32.75-132.62C199.78,814.31,168.75,783.28,131.55,761.77z"
                            />
                        </g>
                        <path
                            class="st0"
                            d="M9,264.25V151.83c0-2.72,2.14-4.95,4.86-5.09c63.81-3.17,115.1-68.37,117.59-132.86
                        c0.11-2.73,2.35-4.89,5.09-4.89h112.39c2.88,0,5.17,2.37,5.11,5.24c-0.89,43.8-12.7,97.97-32.75,132.62
                        c-21.51,37.21-52.54,68.24-89.75,89.75c-34.66,20.04-74.68,31.86-117.38,32.75C11.32,269.42,9,267.09,9,264.25z"
                        />
                    </g>
                </g>
            </svg>

            <span
                class="self-center whitespace-nowrap text-xl font-semibold text-black dark:text-white"
                >KawanDesa</span
            >
        </NavBrand>

        <div class="flex items-center md:order-2">
            <DarkMode class="mr-3" />
            <Button
                id="notification"
                class="mr-3 p-2.5 border-none"
                color="alternative"><BellSolid /></Button
            >
            <Avatar
                id="avatar-menu"
                src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg"
            />
            <!-- <NavHamburger class1="w-full md:flex md:w-auto md:order-1" /> -->
        </div>
        <Dropdown triggeredBy="#notification" class="p-0 max-w-md">
            <Listgroup class="border-none">
                <h3
                    class="text-center p-3 text-sm font-medium text-black bg-gray-50 dark:bg-gray-700 dark:text-white font-bold rounded-t-lg"
                >
                    Notifikasi
                </h3>
                {#each notifs.slice(0, 5) as { id, img, headline, desc, time } (id)}
                    <a href="/">
                        <ListgroupItem class="flex">
                            <Avatar src={img} class="mr-2" />
                            <p
                                class="font-bold mr-1 text-black dark:text-white"
                            >
                                {headline}
                            </p>
                            :
                            <p class="ml-1 truncate">{desc}</p>
                        </ListgroupItem>
                        <hr />
                    </a>
                {/each}
                <a
                    href="/"
                    class="flex items-center justify-center p-3 text-sm font-medium text-black bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white hover:underline rounded-b-lg"
                >
                    <p>Lihat Semua</p>
                </a>
            </Listgroup>
        </Dropdown>
        <Dropdown triggeredBy="#avatar-menu">
            <DropdownHeader>
                <span class="block text-sm"
                    >{$page.props.auth.user.fullName}</span
                >
                <span class="block truncate text-sm font-medium">{role}</span>
            </DropdownHeader>
            <DropdownItem href="/profile">Profile</DropdownItem>
            <DropdownItem on:click={async () => await logout()}
                >Keluar</DropdownItem
            >
        </Dropdown>
    </Navbar>
</header>

<Drawer
    transitionType="fly"
    {backdrop}
    {transitionParams}
    bind:hidden={drawerHidden}
    bind:activateClickOutside
    width="w-64"
    class="overflow-scroll pb-32 overflow-x-hidden mt-16 p-0 border-r-2 z-40"
    id="sidebar"
>
    <div class="flex items-center">
        <CloseButton
            on:click={() => (drawerHidden = true)}
            class="mb- text-black dark:text-white lg:hidden"
        />
    </div>
    <Sidebar asideClass="w-54">
        <SidebarWrapper class="rounded-none pt-0 bg-white">
            <SidebarGroup ulClass={groupClass} class="mb-3">
                {#each filtermenu as { name, icon, children, href, isOpenItems } (name)}
                    {#if children}
                        <SidebarDropdownWrapper
                            label={name}
                            class="pr-3 bg-gray-100 dark:bg-gray-700"
                            isOpen={isOpenItems}
                        >
                            <svelte:component this={icon} slot="icon" />
                            {#each Object.entries(children) as [title, href]}
                                <SidebarItem
                                    label={title}
                                    {href}
                                    spanClass="ml-9"
                                    class={twMerge(
                                        itemClass,
                                        $url == href
                                            ? "bg-gray-100 dark:bg-gray-900"
                                            : "",
                                    )}
                                />
                            {/each}
                        </SidebarDropdownWrapper>
                    {:else}
                        <SidebarItem
                            label={name}
                            {href}
                            spanClass="ml-3"
                            class={twMerge(
                                itemClass,
                                $url == href
                                    ? "bg-gray-100 dark:bg-gray-900"
                                    : "",
                            )}
                        >
                            <svelte:component this={icon} slot="icon" />
                        </SidebarItem>
                    {/if}
                {/each}
            </SidebarGroup>
        </SidebarWrapper>
    </Sidebar>
</Drawer>

<div class="flex px-4 mx-auto w-full">
    <main class="lg:ml-64 mt-4 w-full mx-auto" style="margin-top: 5rem">
        <slot />
    </main>
</div>
<Footer class="absolute bottom-0 start-0 z-20 w-full p-3 border-t-2">
    <FooterCopyright by="Simpang Lima Softwork" />
</Footer>
