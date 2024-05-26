<script lang="ts">
    import {
        NavBrand,
        NavHamburger,
        NavLi,
        NavUl,
        Navbar,
    } from "flowbite-svelte";
    import { ArrowLeftToBracketOutline } from "flowbite-svelte-icons";
    import { onMount } from "svelte";
    import { writable } from "svelte/store";
    import { twMerge } from "tailwind-merge";

    const currentUrl = writable("");
    onMount(() => {
        currentUrl.set(window.location.pathname);
        // currentUrl.subscribe(value =>{
        //     console.log(value);
        // })
    });

    let menus = [
        {
            name: "Home",
            link: "/",
        },
        {
            name: "Profile",
            link: "/lp-profile",
        },
        {
            name: "Pengumuman",
            link: "/lp-pengumuman",
        },
    ];
</script>

<Navbar
    class="px-8 md:px-16 py-2.5 fixed w-full z-20 top-0 start-0 border-b container"
>
    <NavBrand href="/">
        <img src="assets/icons/KD_logo.svg" alt="KawanDesa Logo" class="h-8" />
    </NavBrand>
    <NavHamburger />
    <NavUl>
        {#each menus as menu}
            <NavLi
                href={menu.link}
                class={twMerge(
                    "hover:text-black",
                    $currentUrl == menu.link ? "text-blue-600" : "",
                )}>{menu.name}</NavLi
            >
        {/each}
        <NavLi class="md:sr-only">
            <div class="login">
                <a href="/login" class="flex items-center hover:text-black">
                    <p class="text-blue-600 font-medium">Masuk</p>
                </a>
            </div>
        </NavLi>
    </NavUl>
    <div class="login sr-only md:not-sr-only">
        <a href="/login" class="flex items-center font-bold">
            <ArrowLeftToBracketOutline class="mr-2 font-bold" />
            <p class="opacity-60 hover:opacity-100">Masuk</p>
        </a>
    </div>
</Navbar>
