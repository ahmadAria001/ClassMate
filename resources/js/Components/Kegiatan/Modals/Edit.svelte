<script lang="ts">
    import {
        Button,
        Input,
        Label,
        Modal,
        Textarea,
        Toast,
    } from "flowbite-svelte";

    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";

    import {
        type UpdateSchema,
        updateSchema,
    } from "@R/Utils/Schema/Activity/Update";
    import { createEventDispatcher, onMount } from "svelte";
    import { twMerge } from "tailwind-merge";

    export let showState = false;
    export let target: string;

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

    let data: any;
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const { form, isSubmitting, errors } = createForm<UpdateSchema>({
        extend: validator<UpdateSchema>({
            schema: updateSchema,
        }),
        onSubmit: async (values) => {
            console.log(values);
            let body = {
                id: target,
                name: values.name,
                startDate: new Date(Date.parse(values.startDate)),
                endDate: new Date(Date.parse(values.endDate)),
                location: values.location,
                description: values.description,
                _token: $page.props.csrf_token,
            };

            const response = await axios.put("/api/docs/activity", body);
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
            // showState = false;
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

    const getRequestDocs = async (id: string) => {
        const response = await axios.get(
            `/api/docs/activity/${encodeURIComponent(id)}`,
        );

        return response.data.data;
    };

    onMount(async () => {
        data = await getRequestDocs(target);
    });

    const castDate = (date: number) => {
        return new Date(date * 1000).toISOString().slice(0, -1);
    };
</script>

<Modal title="Ubah Kegiatan" bind:open={showState}>
    <form method="POST" use:form>
        {#if data}
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-5 w-full">
                    <Label for="activityName" class="mb-3">Nama Kegiatan</Label>
                    <Input
                        id="activityName"
                        name="name"
                        placeholder="Nama Kegiatan"
                        value={data.name}
                    />
                    {#if $errors.name}
                        <span class="text-sm text-red-500">{$errors.name}</span>
                    {/if}
                </div>
                <div class="mb-5 w-full">
                    <Label for="location" class="mb-3">Lokasi</Label>
                    <Input
                        id="location"
                        placeholder="Lokasi"
                        name="location"
                        value={data.location}
                    />
                    {#if $errors.location}
                        <span class="text-sm text-red-500"
                            >{$errors.location}</span
                        >
                    {/if}
                </div>
            </div>
            <div class="grid md:grid-cols-2 md:gap-6 w-full">
                <div class="mb-5 w-full">
                    <Label for="time" class="mb-3">Waktu Mulai</Label>
                    <Input
                        type="datetime-local"
                        id="date"
                        name="startDate"
                        placeholder="Tanggal"
                        value={castDate(data.startDate)}
                    />
                    {#if $errors.startDate}
                        <span class="text-sm text-red-500"
                            >{$errors.startDate}</span
                        >
                    {/if}
                </div>
                <div class="mb-5 w-full">
                    <Label for="date" class="mb-3">Waktu Berakhir</Label>
                    <Input
                        type="datetime-local"
                        id="date"
                        name="endDate"
                        placeholder="Tanggal"
                        value={castDate(data.endDate)}
                    />
                    {#if $errors.endDate}
                        <span class="text-sm text-red-500"
                            >{$errors.endDate}</span
                        >
                    {/if}
                </div>
            </div>
            <div class="mb-4">
                <Label for="desc" class="mb-2 text-lg">Deskripsi Kegiatan</Label
                >
                <Textarea
                    rows="2"
                    id="desc"
                    name="description"
                    placeholder="Deskripsi Kegiatan"
                    value={data.docs_id.description}
                />
                {#if $errors.description}
                    <span class="text-sm text-red-500"
                        >{$errors.description}</span
                    >
                {/if}
            </div>

            <div class="block flex">
                <Button type="submit" class="ml-auto">Simpan</Button>
            </div>
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
