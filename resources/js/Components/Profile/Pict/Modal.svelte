<script lang="ts">
    import {
        Button,
        Card,
        Dropzone,
        ImagePlaceholder,
        Input,
        Label,
        Modal,
        Toast,
    } from "flowbite-svelte";

    import { createForm } from "felte";
    import { validateSchema, validator } from "@felte/validator-zod";
    import { page } from "@inertiajs/svelte";

    import {
        type CreateSchema,
        createSchema,
    } from "@R/Utils/Schema/Pict/Create";

    import axiosInstance from "axios";
    import { createEventDispatcher, onMount } from "svelte";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    export let showState: boolean = false;

    const axios = axiosInstance.create({ withCredentials: true });
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const user = $page.props.auth.user;
    const dispatch = createEventDispatcher();

    let avatar: any;
    let value: any[] = [];

    const { form, isSubmitting, errors, createSubmitHandler } =
        createForm<CreateSchema>({
            extend: validator<CreateSchema>({
                schema: createSchema,
            }),
            onSubmit: async (values) => {
                console.log(values);

                const formData = new FormData();
                formData.append("img", values.img);
                formData.append("_token", $page.props.csrf_token);

                const response = await axios.post("/api/user/img/", formData);
                console.log(response);

                err = response.data;
                dispatch("comp");
                showState = false;

                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);
            },
            onError: (values: unknown) => {
                err = {
                    message: values?.response?.data?.message,
                    status: values?.response?.data?.status,
                };
                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);

                console.log(values);

                return;
            },
            onSuccess: () => {
                console.log("Success");
            },
        });

    const onFileSelected = (event: EventTarget) => {
        let image = event?.target?.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(image);
        reader.onload = (e) => {
            avatar = e?.target?.result;
        };
    };

    const dropHandle = (event) => {
        value = [];
        event.preventDefault();
        if (event.dataTransfer.items) {
            [...event.dataTransfer.items].forEach((item, i) => {
                if (item.kind === "file") {
                    const file = item.getAsFile();
                    value.push(file.name);
                    value = value;
                }
            });
        } else {
            [...event.dataTransfer.files].forEach((file, i) => {
                value = file.name;
            });
        }
    };

    const handleChange = (event) => {
        const files = event.target.files;
        if (files.length > 0) {
            value.push(files[0].name);
            value = value;
            onFileSelected(event);
        }
    };

    const showFiles = (files) => {
        if (files.length === 1) return files[0];
        let concat = "";
        files.map((file) => {
            concat += file;
            concat += ",";
            concat += " ";
        });

        if (concat.length > 40) concat = concat.slice(0, 40);
        concat += "...";
        return concat;
    };
</script>

<Modal title="Gambar Profile" bind:open={showState}>
    <form method="POST" use:form>
        <div class="grid max-sm:grid-cols-1 grid-cols-2 gap-5">
            <div class="col-span-1 w-full flex justify-center align-middle">
                {#if !avatar}
                    <ImagePlaceholder imgOnly class="w-full h-full" />
                {:else}
                    <img
                        src={avatar}
                        alt=""
                        class="w-full h-auto object-cover"
                    />
                {/if}
            </div>
            <div class="col-span-1 w-full">
                <Dropzone
                    id="dropzone"
                    on:drop={dropHandle}
                    on:dragover={(event) => {
                        event.preventDefault();
                    }}
                    on:change={handleChange}
                    name="img"
                >
                    <svg
                        aria-hidden="true"
                        class="mb-3 w-10 h-10 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                        ><path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                        /></svg
                    >
                    {#if value.length === 0}
                        <p
                            class="mb-2 text-sm text-gray-500 dark:text-gray-400"
                        >
                            <span class="font-semibold">Click to upload</span> or
                            drag and drop
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            SVG, PNG, JPG or GIF (MAX. 800x400px)
                        </p>
                    {:else}
                        <p>{showFiles(value)}</p>
                    {/if}
                </Dropzone>
            </div>
        </div>

        <div class="w-full flex justify-end">
            <Button
                type="submit"
                id="sbm-img"
                class="mt-3 font-semibold"
                disabled={!isSubmitting}
            >
                Kirim
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    class="w-5 h-5 dark:fill-white ms-2"
                    ><path d="M13 19v-4h3l-4-5-4 5h3v4z"></path><path
                        d="M7 19h2v-2H7c-1.654 0-3-1.346-3-3 0-1.404 1.199-2.756 2.673-3.015l.581-.102.192-.558C8.149 8.274 9.895 7 12 7c2.757 0 5 2.243 5 5v1h1c1.103 0 2 .897 2 2s-.897 2-2 2h-3v2h3c2.206 0 4-1.794 4-4a4.01 4.01 0 0 0-3.056-3.888C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.611 5.757 9.15 3.609 9.792 2 11.82 2 14c0 2.757 2.243 5 5 5z"
                    ></path></svg
                >
            </Button>
        </div>
        {#if $errors.img}
            <span class="text-sm text-red-500">{$errors.img}</span>
        {/if}
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
