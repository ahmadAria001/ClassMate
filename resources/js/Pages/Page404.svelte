<script lang="ts">
    import { Button, P } from "flowbite-svelte";
    export let title = "Halaman tidak ditemukan";
    export let description =
        "Maaf, Kami Tidak Menemukan Halaman yang Anda Cari";
    export let btnTitle = "Kembali";
    export let btnHref = "/beranda";
    export let mainClass = "bg-white dark:bg-gray-900";
    export let mainDivClass =
        "flex flex-col items-center justify-center px-6 pt-8 mx-auto md:h-screen pt:mt-0 bg-white dark:bg-gray-900";
    export let h1Class =
        "mb-3 mt-6 text-2xl font-bold leading-tight text-black sm:text-4xl lg:text-5xl dark:text-white";
    export let imgDiv = "block md:max-w-lg";
    export let div2Class = "text-center xl:max-w-4xl";
    let image = {
        src: "assets/icons/page_not_found.svg",
        alt: "404 Image",
    };
</script>

<main class={mainClass}>
    <div class={mainDivClass}>
        <div class={imgDiv}>
            <img src={image.src} alt={image.alt} />
        </div>
        <div class={div2Class}>
            <h1 class={h1Class}>
                {title}
            </h1>
            <P
                class="mb-5 text-base text-center font-normal text-gray-500 dark:text-gray-400 md:text-lg"
            >
                {description}
            </P>
            <Button href={btnHref}>
                {btnTitle}
            </Button>
        </div>
    </div>
</main>
