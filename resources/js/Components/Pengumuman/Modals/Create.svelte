<script lang="ts">
    import {
        Button,
        Input,
        Label,
        Modal,
        Textarea,
        Toast,
    } from "flowbite-svelte";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";

    import {
        type CreateSchema,
        createSchema,
    } from "@R/Utils/Schema/News/Create";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";
    import { createEventDispatcher } from "svelte";

    export let showState = false;

    const axios = axiosInstance.create({ withCredentials: true });
    const dispatch = createEventDispatcher();

    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const { form, isSubmitting, errors } = createForm<CreateSchema>({
        extend: validator<CreateSchema>({
            schema: createSchema,
        }),
        onSubmit: async (values) => {
            console.log(values);
            let body: any;

            if (values.attachment) {
                const formData = new FormData();
                formData.append("title", values.title);
                formData.append("desc", values.desc);
                formData.append("attachment", values.attachment);
                formData.append("_token", $page.props.csrf_token);

                body = formData;
            } else {
                body = {
                    title: values.title,
                    desc: values.desc,
                    _token: $page.props.csrf_token,
                };
            }
            const response = await axios.post("/api/news", body);
            console.log(response.data);

            err = response.data;
            showState = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        },
        onError: (values: unknown) => {
            err = {
                message: values?.response?.data?.message,
                status: values?.response?.data?.status,
            };
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(values);

            return;
        },
        onSuccess: () => {
            console.log("Success");
        },
    });

    let selectedImage: any | null = null;

    const onFileSelected = (event: EventTarget) => {
        let image = event?.target?.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(image);
        reader.onload = (e) => {
            selectedImage = e?.target?.result;
        };
    };
</script>

<Modal
    title="Tambah Pengumuman"
    bind:open={showState}
    on:close={() => (selectedImage = null)}
>
    <form method="POST" use:form>
        <div class="mb-4">
            <Label for="titleAnnouncement" class="mb-2">Judul Pengumuman</Label>
            <Input
                id="titleAnnouncement"
                name="title"
                placeholder="Judul Pengumuman"
            />
            {#if $errors.title}
                <span class="text-sm text-red-500">{$errors.title}</span>
            {/if}
        </div>
        <div class="mb-4">
            <Label for="desc" class="mb-2">Isi Pengumuman</Label>
            <Textarea
                rows="2"
                id="desc"
                name="desc"
                placeholder="Isi Pengumuman"
            />
            {#if $errors.desc}
                <span class="text-sm text-red-500">{$errors.desc}</span>
            {/if}
        </div>
        <div class="mb-4">
            <img
                src={selectedImage}
                alt="Selected Image"
                class={selectedImage
                    ? "w-full h-auto object-cover rounded-lg border-gray-600 border-2"
                    : "hidden"}
            />

            <div
                class={!selectedImage
                    ? "flex items-center justify-center w-full bg-upload"
                    : "h-20 overflow-hidden mt-2 bg-upload"}
                style=""
            >
                <label
                    for="dropzone-file"
                    class="flex flex-col items-center justify-center w-full h-64 max-h-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600"
                >
                    <div
                        class="flex flex-col items-center justify-center pt-5 pb-6"
                    >
                        <svg
                            class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400"
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
                        <p
                            class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-semibold"
                        >
                            Upload Gambar
                        </p>
                    </div>
                    <input
                        id="dropzone-file"
                        type="file"
                        name="attachment"
                        class="hidden"
                        on:change={(e) => onFileSelected(e)}
                        accept="image/*"
                    />
                </label>
            </div>
            {#if $errors.attachment}
                <span class="text-sm text-red-500">{$errors.attachment}</span>
            {/if}
        </div>
        <div class="block flex">
            <Button type="submit" class="ml-auto" disabled={!isSubmitting}
                >Simpan</Button
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
