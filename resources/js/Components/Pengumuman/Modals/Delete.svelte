<script lang="ts">
    import { Button, Input, Label, Modal, Toast } from "flowbite-svelte";

    import axiosInstance from "axios";
    import {
        CheckCircleSolid,
        CloseCircleSolid,
        ExclamationCircleOutline,
    } from "flowbite-svelte-icons";
    import { twMerge } from "tailwind-merge";

    import { page } from "@inertiajs/svelte";
    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";
    import {
        type DeleteSchema,
        deleteSchema,
    } from "@R/Utils/Schema/News/Delete";
    import { createEventDispatcher, onMount } from "svelte";

    export let showState = false;
    export let target: string;

    const axios = axiosInstance.create({ withCredentials: true });
    const dispatch = createEventDispatcher();

    let confirm = false;
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const getNewsData = async (id: string = "") => {
        const response = await axios.get(
            `/api/news/${encodeURIComponent(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const { form, isSubmitting, errors } = createForm<DeleteSchema>({
        extend: validator<DeleteSchema>({
            schema: deleteSchema,
        }),
        onSubmit: async (values) => {
            console.log(values);
            const response = await axios.delete("/api/news", {
                data: {
                    id: values.id,
                    _token: $page.props.csrf_token,
                },
            });

            err = response.data;
            showState = false;
            confirm = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: unknown) => {
            console.error(values?.response);
            err = values?.response?.data;

            confirm = false;
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(values);
            return;
        },
        onSuccess: (response) => {
            console.log("Success");
        },
    });
</script>

<Modal bind:open={showState} size="sm">
    <form method="post" use:form>
        {#await getNewsData(target) then data}
            <div class={twMerge("text-center", confirm ? "hidden" : "")}>
                <ExclamationCircleOutline
                    class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
                />
                <h3
                    class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400"
                >
                    Apa anda yakin ingin menghapus data ini?
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div class="mb-4 hidden">
                        <Label for="id" class="mb-2">ID</Label>
                        <Input
                            type="number"
                            id="id"
                            name="id"
                            readonly
                            value={target}
                        />
                        {#if $errors.id}
                            <span class="text-sm text-red-500"
                                >{$errors.id}</span
                            >
                        {/if}
                    </div>
                    <div class="mb-4">
                        <Label for="number" class="mb-2">Judul</Label>
                        <Input
                            type="text"
                            name="title"
                            id="number"
                            disabled
                            value={data.data.title}
                        />
                    </div>

                    <div class="mb-4">
                        <Label for="leader" class="mb-2">Isi Pengumuman</Label>
                        <Input
                            id="leader"
                            name="leader"
                            disabled
                            value={data.data.desc}
                        />
                    </div>
                    {#if data.data.attachment}
                        <div class="mb-4">
                            <img
                                src={`/assets/uploads/${data.data.attachment}`}
                                alt="Selected Image"
                                class="w-full h-auto max-h-72 mb-3 rounded-lg border-2 border-gray-500"
                            />
                        </div>
                    {/if}
                </div>

                <Button
                    color="red"
                    class="me-2"
                    on:click={() => {
                        confirm = true;
                    }}>Selanjutnya</Button
                >
                <Button color="alternative">Batal</Button>
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
                <Button
                    color="red"
                    type="submit"
                    disabled={$isSubmitting}
                    class="me-2">Ya, yakin</Button
                >
                <Button color="alternative" on:click={() => (showState = false)}
                    >Tidak, batal</Button
                >
            </div>
        {/await}
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
