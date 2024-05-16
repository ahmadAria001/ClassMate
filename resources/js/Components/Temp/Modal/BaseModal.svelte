<script lang="ts">
    import { twMerge } from "tailwind-merge";

    export let showState = false;
    export let id = "";
</script>

<div
    {id}
    class={twMerge(
        "w-full z-[100] h-full bg-gray-900 bg-opacity-80 top-0 fixed sticky-0",
        showState ? "" : "hidden",
    )}
>
    <div
        class="2xl:container 2xl:mx-auto py-48 px-4 md:px-28 flex justify-center items-center"
    >
        <div
            class="w-96 rounded-lg md:w-auto dark:bg-gray-800 relative flex flex-col justify-center items-center bg-white py-16 px-4 md:px-24 xl:py-24 xl:px-36"
        >
            <slot />
            <button
                on:click={() => (showState = false)}
                class="text-gray-800 dark:text-gray-400 absolute top-8 right-8 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800"
                aria-label="close"
            >
                <svg
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M18 6L6 18"
                        stroke="currentColor"
                        stroke-width="1.66667"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M6 6L18 18"
                        stroke="currentColor"
                        stroke-width="1.66667"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </button>
        </div>
    </div>
</div>
