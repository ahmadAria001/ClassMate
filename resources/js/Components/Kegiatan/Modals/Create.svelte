<script lang="ts">
    import { Button, Input, Label, Modal, Textarea } from "flowbite-svelte";

    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";

    import {
        type CreateSchema,
        createSchema,
    } from "@R/Utils/Schema/Activity/Create";
    import { createEventDispatcher, onMount } from "svelte";
    import { twMerge } from "tailwind-merge";

    export let showState = false;

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

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
            let body = {
                name: values.name,
                startDate: new Date(Date.parse(values.startDate)),
                endDate: new Date(Date.parse(values.endDate)),
                location: values.location,
                description: values.description,
                _token: $page.props.csrf_token,
            };

            try {
                const response = await axios.post("/api/docs/activity", body);
                console.log(response.data);

                err = response.data;
                showState = false;
                dispatch("comp");

                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);
            } catch (error) {
                err = {
                    message: error?.response?.data?.message,
                    status: error?.response?.data?.status,
                };

                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);

                console.error(error);
            }
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

    onMount(() => {
        // fillDate();
    });

    const fillDate = () => {
        const dates = document.querySelectorAll("#date");

        if (dates.length < 1) return;

        dates.forEach(
            (dtp) =>
                (dtp.value = new Date(Date.now()).toISOString().slice(0, -1)),
        );
    };
</script>

<Modal title="Tambah Kegiatan" bind:open={showState}>
    <form method="POST" use:form>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-5 w-full">
                <Label for="activityName" class="mb-3">Nama Kegiatan</Label>
                <Input
                    id="activityName"
                    name="name"
                    placeholder="Nama Kegiatan"
                />
                {#if $errors.name}
                    <span class="text-sm text-red-500">{$errors.name}</span>
                {/if}
            </div>
            <div class="mb-5 w-full">
                <Label for="location" class="mb-3">Lokasi</Label>
                <Input id="location" placeholder="Lokasi" name="location" />
                {#if $errors.location}
                    <span class="text-sm text-red-500">{$errors.location}</span>
                {/if}
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6 w-full">
            <div class="mb-5 w-full">
                <Label for="time" class="mb-3">Waktu</Label>
                <Input
                    type="datetime-local"
                    id="date"
                    name="startDate"
                    placeholder="Tanggal"
                />
                {#if $errors.startDate}
                    <span class="text-sm text-red-500">{$errors.startDate}</span
                    >
                {/if}
            </div>
            <div class="mb-5 w-full">
                <Label for="date" class="mb-3">Tanggal</Label>
                <Input
                    type="datetime-local"
                    id="date"
                    name="endDate"
                    placeholder="Tanggal"
                />
                {#if $errors.endDate}
                    <span class="text-sm text-red-500">{$errors.endDate}</span>
                {/if}
            </div>
        </div>
        <div class="mb-4">
            <Label for="desc" class="mb-2 text-lg">Deskripsi Kegiatan</Label>
            <Textarea
                rows="2"
                id="desc"
                name="description"
                placeholder="Deskripsi Kegiatan"
            />
            {#if $errors.description}
                <span class="text-sm text-red-500">{$errors.description}</span>
            {/if}
        </div>

        <div class="block flex">
            <Button type="submit" class="ml-auto">Simpan</Button>
        </div>
    </form>
</Modal>
