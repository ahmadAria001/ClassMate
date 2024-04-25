<script lang="ts">
    import axiosInstance from "axios";

    import {
        Modal,
        Input,
        Label,
        Button,
        Select,
        Toggle,
        Toast,
    } from "flowbite-svelte";
    import {
        CheckCircleSolid,
        CloseCircleSolid,
        ExclamationCircleOutline,
    } from "flowbite-svelte-icons";
    import Delete from "./Confirmation.svelte";

    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validateSchema, validator } from "@felte/validator-zod";
    import {
        type DeleteSchema,
        deleteSchema,
    } from "../../../Utils/Schema/Civils/Delete";

    import { twMerge } from "tailwind-merge";
    import { createEventDispatcher } from "svelte";

    export let showState = false;
    export let data: any | null = null;
    const dispatch = createEventDispatcher();

    let confirm = false;
    let status: string | null = null;

    const axios = axiosInstance.create({ withCredentials: true });
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const { form, isSubmitting, errors } = createForm<DeleteSchema>({
        extend: validator<DeleteSchema>({
            schema: deleteSchema,
        }),
        onSubmit: async (values) => {
            const response = await axios.delete("/api/civilian", {
                data: {
                    id: values.id,
                    status: values.status,
                    _token: $page.props.csrf_token,
                },
            });

            err = response.data;
            showState = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: unknown) => {
            console.error(values?.response);
            err = values?.response?.data;

            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            return;
        },
        onSuccess: (response) => {
            console.log("Success");
        },
    });

    let delReasons = [
        { value: "pindah", name: "Pindah" },
        { value: "meninggal", name: "Meninggal" },
    ];
</script>

<Modal bind:open={showState} size="sm">
    <form method="post" use:form>
        <input
            type="text"
            readonly
            name="id"
            value={data.data.id}
            class="hidden"
        />
        <div class={twMerge("text-center", confirm ? "hidden" : "")}>
            <ExclamationCircleOutline
                class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
            />
            <h3
                class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
            >
                Apa alasan data warga 'nama' dihapus?
            </h3>
            <Select
                class="my-2"
                items={delReasons}
                id="status"
                name="status"
                placeholder="Alasan dihapus"
                size="sm"
            />
            <Button
                color="red"
                class="me-2"
                on:click={() => {
                    confirm = true;
                }}>Selanjutnya</Button
            >
            <Button color="alternative" on:click={() => (showState = false)}
                >Batal</Button
            >
        </div>

        <div class={twMerge("text-center", confirm ? "" : "hidden")}>
            <ExclamationCircleOutline
                class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
            />
            <h3
                class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
            >
                Apakah yakin ingin menghapus warga ini?
            </h3>
            <Button color="red" type="submit" class="me-2">Ya, yakin</Button>
            <Button color="alternative" on:click={() => (showState = false)}
                >Tidak, batal</Button
            >
        </div>
    </form>
</Modal>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
