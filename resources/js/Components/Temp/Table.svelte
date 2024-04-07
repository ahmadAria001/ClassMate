<script>
    import { twMerge } from "tailwind-merge";

    let text = "Hello World!";
    export let tableClass;
</script>

<table
    class={twMerge([
        "w-full text-md bg-white shadow-md rounded mb-4",
        tableClass,
    ])}
>
    <slot />
</table>
