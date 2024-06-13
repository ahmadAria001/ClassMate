<script lang="ts">
    import { Button, Card, Fileupload, Toast } from "flowbite-svelte";

    import { page } from "@inertiajs/svelte";
    import PictModal from "@C/Profile/Pict/Modal.svelte";
    import { createEventDispatcher } from "svelte";
    import { twMerge } from "tailwind-merge";

    export let data: any;

    const dispatch = createEventDispatcher();
    const user = $page.props.auth.user;

    let showModal: boolean = false;
    let builder = {};

    console.log(user);

    export const rebuild = () => {
        builder = {};

        location.reload();
        dispatch("comp");
    };
</script>

<Card
    class={twMerge(
        "max-w-full flex flex-row mb-6 bg-none dark:bg-none bg-center",
    )}
>
    {#if data.data?.length > 0}
        {#key builder}
            <img
                src={!data.data[0].pict
                    ? "https://t3.ftcdn.net/jpg/05/16/27/58/360_F_516275801_f3Fsp17x6HQK0xQgDQEELoTuERO4SsWV.jpg"
                    : `${$page.props.auth.user.pict}`}
                alt="Foto profil"
                class="w-24 h-24 rounded-lg mr-3 object-center border-white border-2"
            />
        {/key}
        <div>
            <h5
                class="font-bold text-black dark:text-white text-xl truncate max-w-xs md:max-w-md"
            >
                {user.fullName} ({user.username})
            </h5>
            <p
                class="font-light text-black dark:text-white outline-white dark:outline-black"
            >
                {data.data[0].civilian_id.nik}
            </p>
            <Button
                size="sm"
                class="mt-2"
                type="button"
                on:click={() => {
                    showModal = true;
                }}
            >
                <svg
                    class="w-4 h-4 text-white mr-2"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 20 16"
                >
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"
                    />
                </svg>
                Upload Foto
            </Button>
        </div>
    {/if}
</Card>
<PictModal bind:showState={showModal} on:comp={rebuild} />
