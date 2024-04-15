<script lang="ts">
    import { onMount } from "svelte";
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

    // tempat rolenya disini
    let role = "Warga";

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

    let site = {
        name: "KawanDesa",
        img: "assets/icons/KD_logo.svg",
        imgDark: "assets/icons/KD_logo.svg",
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
</script>

<svelte:window bind:innerWidth={width} />
<header class="flex-none w-full mx-auto bg-white dark:bg-slate-950">
    <Navbar let:hidden let:toggle class="border-b-2 h-16 fixed">
        <NavHamburger
            on:click={toggleDrawer}
            btnClass="focus:outline-none whitespace-normal rounded-lg focus:ring-2 p-1.5 focus:ring-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 m-0 mr-3 lg:hidden"
        />
        <NavBrand href="/" class="md:w-64">
            <img src={site.img} class="me-3 h-6 sm:h-9" alt={site.imgAlt} />
            <!-- <img
                src={site.imgDark}
                class="me-3 h-6 sm:h-9 hidden dark:block"
                alt={site.imgAlt}
            /> -->
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
                <span class="block text-sm">Muhammad Fatoni</span>
                <span class="block truncate text-sm font-medium"
                    >fatonigaming@gmail.com</span
                >
            </DropdownHeader>
            <DropdownItem href="/profile">Profile</DropdownItem>
            <DropdownItem href="/login">Keluar</DropdownItem>
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
    class="overflow-scroll pb-32 overflow-x-hidden mt-16 p-0 border-r-2"
    id="sidebar"
>
    <div class="flex items-center">
        <CloseButton
            on:click={() => (drawerHidden = true)}
            class="mb-4 dark:text-white lg:hidden"
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
                                    class={itemClass}
                                />
                            {/each}
                        </SidebarDropdownWrapper>
                    {:else}
                        <SidebarItem
                            label={name}
                            {href}
                            spanClass="ml-3"
                            class={itemClass}
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
