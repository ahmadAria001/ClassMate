<script lang="ts">
    import {
        Navbar,
        NavBrand,
        NavLi,
        NavUl,
        NavHamburger,
        Avatar,
        Dropdown,
        DropdownItem,
        DropdownHeader,
        DropdownDivider,
        Input,
        Sidebar,
        SidebarGroup,
        SidebarItem,
        SidebarWrapper,
        SidebarDropdownItem,
        SidebarDropdownWrapper,
        DarkMode,
    } from "flowbite-svelte";
    import {
        ChartPieSolid,
        UsersGroupSolid,
        FileSolid,
        ClipboardSolid,
        VolumeUpSolid,
    } from "flowbite-svelte-icons";

    let posts = [
        { name: "Beranda", icon: ChartPieSolid, href: "/dashboard" },
        {
            name: "Data Warga",
            icon: UsersGroupSolid,
            isOpenItems: true,
            children: {
                "Lihat Data Warga": "",
                "Arsip Warga": "",
            },
        },
        {
            name: "Pengaduan",
            icon: FileSolid,
            isOpenItems: true,
            children: {
                "Daftar Pengadan": "",
            },
        },
        {
            name: "Laporan",
            icon: ClipboardSolid,
            isOpenItems: true,
            children: {
                "Surat Keterangan": "",
                "Laporan keuangan": "",
                "Bantuan sosial": "",
            },
        },
        {
            name: "Informasi",
            icon: VolumeUpSolid,
            isOpenItems: true,
            children: {
                Pengumuman: "",
                "Kegiatan Warga": "",
            },
        },
    ];

    let site = {
        name: "KawanDesa",
        img: "assets/icons/KD_logo.svg",
        link: "/",
        imgAlt: "KawanDesa Logo",
    };

    let iconClass =
        "flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white";
    let itemClass =
        "flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 group dark:text-gray-200 dark:hover:bg-gray-700";
    let groupClass = "pt-2 space-y-2";
</script>

<Navbar class="flex flex-col border border-b-2 grid justify-items-stretch">
    <NavBrand href="/" class="md:w-64">
        <img src={site.img} class="me-3 h-6 sm:h-9" alt={site.imgAlt} />
        <span
            class="self-center whitespace-nowrap text-xl font-semibold dark:text-white"
            >KawanDesa</span
        >
    </NavBrand>
    <div class="flex items-center md:order-2">
        <DarkMode class="mr-3" />
        <Avatar
            id="avatar-menu"
            src="https://images.pexels.com/photos/614810/pexels-photo-614810.jpeg?cs=srgb&dl=pexels-simon-robben-614810.jpg&fm=jpg"
        />
        <!-- <NavHamburger class1="w-full md:flex md:w-auto md:order-1" /> -->
    </div>
    <Dropdown placement="bottom" triggeredBy="#avatar-menu">
        <DropdownHeader>
            <span class="block text-sm">Muhammad Fatoni</span>
            <span class="block truncate text-sm font-medium"
                >fatonigaming@gmail.com</span
            >
        </DropdownHeader>
        <DropdownItem>Profile</DropdownItem>
        <!-- <DropdownDivider /> -->
        <DropdownItem>Keluar</DropdownItem>
    </Dropdown>
</Navbar>

<Sidebar>
    <SidebarWrapper class="rounded-none">
        <SidebarGroup ulClass={groupClass} class="mb-3">
            {#each posts as { name, icon, children, href, isOpenItems } (name)}
                {#if children}
                    <SidebarDropdownWrapper
                        label={name}
                        class="pr-3"
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
